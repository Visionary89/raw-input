/* tested on mysql 7.5 */
CREATE TABLE IF NOT EXISTS orders (
  id SERIAL,
  description TEXT,
  raw_phone_numbers VARCHAR(255) default ''
);

CREATE TABLE IF NOT EXISTS phone_numbers (
  id SERIAL,
  `number` CHAR(10) UNIQUE
);

CREATE TABLE IF NOT EXISTS orders_phone_numbers_pivot (
  order_id BIGINT UNSIGNED NOT NULL,
  phone_number_id BIGINT UNSIGNED NOT NULL,
  UNIQUE KEY phone_number_id_order_id_ui (phone_number_id, order_id),
  KEY order_id_i (order_id)
);