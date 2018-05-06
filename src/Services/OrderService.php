<?php


namespace RawInput\Services;

use RawInput\Types\PhoneNumberCollection;
use RawInput\Types\Text;

/**
 * Class      OrderService
 * @package RawInput\Services
 */
class OrderService
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        // в идеале это должен быть репозиторий
        $this->pdo = $pdo;
    }

    public function createOrder(PhoneNumberCollection $phoneNumberCollection, Text $description)
    {
        $sql = "INSERT INTO orders SET raw_phone_numbers = :phone_numbers, description = :description";

        $this->pdo->prepare($sql)->execute([
            ':phone_numbers' => $phoneNumberCollection->getValue(),
            ':description'   => $description->getSanitizedValue(),
        ]);

        $orderId = (int)$this->pdo->lastInsertId();
        $this->assignUsers($phoneNumberCollection, $orderId);

        return $orderId;
    }

    private function assignUsers(PhoneNumberCollection $phoneNumberCollection, int $orderId)
    {
        $inQuery = implode(',', array_fill(0, count($phoneNumberCollection->getSanitizedValue()), '?'));

        $statement = $this->pdo->prepare('SELECT * FROM phone_numbers WHERE `number` IN (' . $inQuery . ')');

        $statement->execute($phoneNumberCollection->getSanitizedValue());

        $existsNumbers = $statement->fetchAll(\PDO::FETCH_ASSOC);

        //выбираем телефонные номера, для заведения в бд
        $newNumbers = array_diff($phoneNumberCollection->getSanitizedValue(), array_column($existsNumbers, 'number'));
        if (!empty($newNumbers)) {
            $addNumberQuery = $this->pdo->prepare('INSERT INTO phone_numbers SET `number` = :phone_number');
            foreach ($newNumbers as $phoneNumber) {
                $addNumberQuery->execute([':phone_number' => $phoneNumber]);
                $existsNumbers[] = [
                    'id'     => $this->pdo->lastInsertId(),
                    'number' => $phoneNumber,
                ];
            }
        }

        $assignQuery = $this->pdo->prepare('INSERT INTO orders_phone_numbers_pivot SET phone_number_id = :phone_number_id, order_id = :order_id');

        foreach ($existsNumbers as $existsNumber) {
            $assignQuery->execute([
                ':phone_number_id' => $existsNumber['id'],
                ':order_id'        => $orderId,
            ]);
        }
    }

    public function getPhoneNumbers(int $orderId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT `number` FROM phone_numbers WHERE id IN (
              SELECT phone_number_id FROM orders_phone_numbers_pivot WHERE order_id = :order_id
            )'
        );
        $statement->execute([':order_id' => $orderId]);
        return array_column($statement->fetchAll(\PDO::FETCH_ASSOC), 'number');
    }

    public function getOrders(PhoneNumberCollection $phoneNumberCollection)
    {
        $inQuery = implode(',', array_fill(0, count($phoneNumberCollection->getSanitizedValue()), '?'));
        $statement = $this->pdo->prepare(
            'SELECT orders.* FROM orders JOIN orders_phone_numbers_pivot on order_id = orders.id WHERE phone_number_id IN (
              SELECT id FROM phone_numbers WHERE `number` IN (' . $inQuery . ')
            )'
        );

        $statement->execute($phoneNumberCollection->getSanitizedValue());

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
