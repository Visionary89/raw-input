<?php

use RawInput\Services\OrderService;

/**
 * Class      OrderServiceTest
 * Заведомо не верно структурированный тест, тем не менее позволяет протестировать основной функционал.
 * По сути этот тест заменяет контролер
 */
class OrderServiceTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var \PDO
     */
    private static $pdo;
    /**
     * @var OrderService
     */
    protected $service;

    public static function setUpBeforeClass()
    {
        // В иделае это должен быть mock object создаваемый в рамках отдельных тестов
        self::$pdo = new \PDO(
            $_ENV['db_host'] ?: 'mysql:dbname=raw_input;unix_socket=/tmp/mysql.sock',
            $_ENV['db_username'] ?: 'raw_input',
            $_ENV['db_password'] ?: 'password',
            $_ENV['db_options'] ?: null
        );

        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        static::$pdo->beginTransaction();
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        static::$pdo->rollBack();
        parent::tearDownAfterClass();
    }

    protected function setUp()
    {
        $this->service = new OrderService(static::$pdo);
        parent::setUp();
    }


    public function orderProvider()
    {
        return [
            [
                'raw_phone_numbers' => '9031234567',
                'description'       => 'Очень нужен преподаватель информатики',
            ],
            [
                'raw_phone_numbers' => '9031234567, 9161233344',
                'description'       => 'Очень нужен руководитель енотов',
            ],
        ];
    }

    /**
     * @param $number
     * @param $text
     * @dataProvider orderProvider
     */
    public function testCreateOrder($number, $text)
    {
        $phoneCollection = new \RawInput\Types\PhoneNumberCollection($number);
        $text = new \RawInput\Types\Text($text);
        $orderId = $this->service->createOrder($phoneCollection, $text);
        $this->assertGreaterThan(0, $orderId);

        $phoneNumbers = $this->service->getPhoneNumbers($orderId);

        $this->assertEquals(explode(', ', $number), $phoneNumbers);
    }

    /**
     * @depends testCreateOrder
     */
    public function testGetOrders()
    {
        $phoneNumbers = new \RawInput\Types\PhoneNumberCollection($this->orderProvider()[0]['raw_phone_numbers']);
        $orders = $this->service->getOrders($phoneNumbers);
        $this->assertArraySubset($this->orderProvider(), $orders);
    }
}
