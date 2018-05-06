#Тестовое задание поступление заказа с номером телефона
##Постановка задачи

> Задача состоит из двух подчастей, первая преимущественно на уровень владения PHP, вторая — MySQL. Необходимо самостоятельно формализовать задачу, придумать структуры данных, форматы, модульность, интерфейс (без деталей) и всё остальное, что может понадобиться для реального применения вашего решения этой вполне реальной задачи. Итак:
>
> С типовой формы заказа (такой как на главной странице repetitors.info) в базу данных поступают заполненные бланки, содержащие, в частности, графу «номер телефона». Содержимое этой графы — строка, полнейший user input, никак не структурированный, не отфильтрованный и способный содержать всё что угодно в каком угодно виде, но где-то там в этой строке пользователь наверняка указал в том числе и свой номер телефона (или несколько).
> 
> Необходимо аккуратно распарсить этот user input, чтобы вытащить оттуда все номера телефонов и привести их к каноническому виду:
> 
> 8KKKNNNNNNN (например, 89031110022)
> 
> Все номера по формату российские. Код города по умолчанию (для прямых номеров) считать заданным (например, пусть будет 495). 
> 
> При поступлении нового заказа менеджеру нужно знать, есть ли в базе другие заказы от этого же клиента. Это могут быть дубликаты того же заказа, предыдущие старые заказы этого клиента и пр. — не важно. Важно то, что будем для простоты считать, что клиент идентифицируется только по номерам телефона. Нужно придумать и описать (словами) механизм поиска заказов от того же клиента, что прислал заданный заказ. В описании должны присутствовать: общая идея решения, структура БД, характерные запросы.
> 
> Сразу уточним, что задача хоть и небольшая (рассчитана на пару часов работы и пару страниц кода), но весьма непростая, т.к. требует самостоятельного нахождения всех возможных подвохов (особенно в первой части) и создания оптимальных универсальных структур данных и кода. Предполагаем, что база данных большая, обращений к скрипту много, и вообще всё живёт в условиях почти high load. 
> 
> UPD.-2012: «Пара страниц» — это буквально пара страниц. Решения в 100 файлов суммарным объёмом 500 кб невозможно проверить. Решения, использующие разные мудрёные фреймворки, также невозможно проверить. Чистый PHP, чистый MySQL, стандартные библиотеки — для задачки на пару страниц этого более чем достаточно.

##Разбор и анализ задачи. Допустимые ограничения.

* Для простоты предположим, что вся остальная информация о заказе это текст с textarea. И допустимым текстомможет быть любой текст от 10 символов.
* При приеме пользовательских данных для их нормализации есть два подхода: проверять верность следования формату, возвращая пользователю сообщение о ошибках с отказом в приеме данных, и нормализация данных внутреними средствами. В решении будем рассматривать последний подход.  
* В общем случае однозначно определить принаблежность телефонного номера одному и тому же пользователю сложная задача. Телефонный номер мог перейти к новому пользователю, у нескольких пользователей может быть общий номер телефона.
* В описапнии задачи сказано о необходимости обработки user input. В рамках подхода к решению стоит так же сохранять весь user input без изменений, т.к. при вычленении только номеров телефонов из этого поля может пропасть важная информация. Например: комментарий об удобном времени для звонка, дополнительном номере (общий телефоный номер фирмы + номер аппарата сотрудника). Кроме того в этом поле может приходить сразу несколько телефонных номеров.
* Менеджер, обрабатывающий заказ, должен первым шагом убедиться что user input аккуратно распознан. Менеджер сможет найти заказы прикрепленные к любому из распознанных номеров. Кроме того достаточно легко вывести данные о заказах, соответствующих всем номерам или хотя бы одному (предполагается что такой необходимости все же нет).

## Формализация задачи
Необходимо разработать систему пользовательских типов данных (с валидацией и санитаризацией).
Типы данных:
* текст
* коллекция телефонных номеров

Кроме того необходимо разработать схему базы данных состоящей из сущностей:
* phone_numbers - правочник телефонных номеров
* orders - заказы
* orders_phone_numbers_pivot - таблица участия телефонных номеров в заказе

##Как проверять решение
Для простоты проверки и независимости от внешних библиотек проверка производится на актуальной базе данных. Для этого можно размернуть ее копию из миграционного файла (система миграция для простоты тоже отсутствует)

```mysql -uraw_input -ppassword raw_input <  db/structure.sql``` 

В проекте есть зависимость от phpunit именно для осуществления тестов

```composer install```

Далее необходимо запустить тесты:

```./vendor/bin/phpunit```

Как и в боевом проекте тестами покрыта не каждая строчка кода, а только критичные участки.
