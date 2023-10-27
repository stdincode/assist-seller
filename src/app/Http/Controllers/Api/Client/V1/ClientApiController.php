<?php
/** @noinspection PhpAttributeCanBeAddedToOverriddenMemberInspection */

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(version: "0.1", title: "Публичное API сервиса Assist Seller")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    in: "header",
    bearerFormat: "JWT",
    scheme: "bearer"
)]

#[OA\Schema(
    schema: 'student',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор студента', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Шурик"),
        new OA\Property(property: 'contact_phone_number', description: 'Номер телефона для контакта', type: 'string', format: 'string', example: "79223334455", nullable: true),
        new OA\Property(property: 'is_blocker', description: 'является ли блокированным', type: 'bool', format: 'bool', example: false),
        new OA\Property(property: 'expenses', description: 'Расходы', type: 'float', format: 'float', example: 550.95, nullable: true),
        new OA\Property(property: 'telegram_client', ref: '#/components/schemas/telegramClient'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/timestamp', description: 'Дата и время создания'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/timestamp', description: 'Дата и время обновления', nullable: true),
    ],
    type: 'object',
    nullable: true
)]
#[OA\Schema(
    schema: 'expert',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор эксперта', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Владимир"),
        new OA\Property(property: 'last_name', description: 'Фамилия', type: 'string', format: 'string', example: "Тотсамый"),
        new OA\Property(property: 'patronymic', description: 'Отчество', type: 'string', format: 'string', example: "Владимирович"),
        new OA\Property(property: 'biography', description: 'Биография', type: 'string', format: 'string', example: "Царь, очень приятно, царь"),
        new OA\Property(property: 'telegram_phone_number', description: 'Номер телефона в telegram', type: 'string', format: 'string', example: "79001112233"),
        new OA\Property(property: 'whatsapp_phone_number', description: 'Номер телефона в what\'s app', type: 'string', format: 'string', example: "79001112233"),
        new OA\Property(property: 'balance', description: 'Баланс', type: 'float', format: 'float', example: 9999999.99),
        new OA\Property(property: 'is_verification', description: 'является ли верифицированным', type: 'bool', format: 'bool', example: true),
        new OA\Property(property: 'is_blocker', description: 'является ли блокированным', type: 'bool', format: 'bool', example: false),
        new OA\Property(property: 'telegram_client', ref: '#/components/schemas/telegramClient'),
        new OA\Property(property: 'price_work_hour', description: 'Стоимость часа работы', type: 'float', format: 'float', example: 999999.99, nullable: true),
        new OA\Property(property: 'avatar', description: 'Путь к аватарке', type: 'string', format: 'string', example: "avatars/3241dad3141f1f", nullable: true),
        new OA\Property(property: 'video', description: 'Путь к видео', type: 'string', format: 'string', example: "videos/123g5fds3242gsg", nullable: true),
        new OA\Property(property: 'requisites', description: 'Реквизиты', type: 'string', format: 'string', example: "номер карты: 1111-4444-5555-9999", nullable: true),
        new OA\Property(
            property: 'payments',
            description: 'Выплаты',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/expertPayment', type: 'object'),
            nullable: true
        ),
        new OA\Property(
            property: 'places',
            description: 'Площадки',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/place', type: 'object'),
            nullable: true
        ),
        new OA\Property(
            property: 'specializations',
            description: 'Специализации',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/place', type: 'object'),
            nullable: true
        ),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/timestamp', description: 'Дата и время создания'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/timestamp', description: 'Дата и время обновления', nullable: true),
    ],
    type: 'object',
    nullable: true
)]
#[OA\Schema(
    schema: 'expertPayment',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор выплаты эксперту', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'amount', description: 'Сумма выплаты', type: 'float', format: 'float', example: "500.99"),
        new OA\Property(property: 'status', ref: '#/components/schemas/expertPaymentStatus'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/timestamp', description: 'Дата и время создания'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/timestamp', description: 'Дата и время обновления', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'expertPaymentStatus',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор статуса', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'name', description: 'Название статуса', type: 'string', format: 'string', example: 'Запрос'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'telegramClient',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор нашего клиента telegram', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'telegram_id', description: 'Реальный идентификатор telegram', type: 'integer', format: 'integer', example: 1213214551),
        new OA\Property(property: 'telegram_first_name', description: 'Имя в telegram', type: 'string', format: 'string', example: 'Vladimir', nullable: true),
        new OA\Property(property: 'telegram_last_name', description: 'Фамилия в telegram', type: 'string', format: 'string', example: 'Yep', nullable: true),
        new OA\Property(property: 'telegram_username', description: 'Имя пользователя в telegram', type: 'string', format: 'string', example: '@Vlad', nullable: true),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/timestamp', description: 'Дата и время создания'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/timestamp', description: 'Дата и время обновления', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'place',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор площадки', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'name', description: 'Название площадки', type: 'string', format: 'string', example: 'yandex'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'specialization',
    properties: [
        new OA\Property(property: 'id', description: 'Идентификатор специализации', type: 'integer', format: 'integer', example: 1),
        new OA\Property(property: 'name', description: 'Название специализации', type: 'string', format: 'string', example: 'data science'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'timestamp',
    properties: [
        new OA\Property(property: 'date', description: 'Дата и время', type: 'string', format: 'integer', example: "2023-03-22 17:28:43.000000"),
        new OA\Property(property: 'timezone_type', description: 'Тип timezone', type: 'integer', format: 'integer', example: 3),
        new OA\Property(property: 'timezone', description: 'Timezone часового пояса', type: 'string', format: 'string', example: 'UTC'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'indexResponseMessage',
    description: 'Сообщение',
    type: 'string',
    example: 'found'
)]
#[OA\Schema(
    schema: 'createdResponseMessage',
    description: 'Сообщение',
    type: 'string',
    example: 'created'
)]
#[OA\Schema(
    schema: 'updateResponseMessage',
    description: 'Сообщение',
    type: 'string',
    example: 'updated'
)]
#[OA\Schema(
    schema: 'deleteResponseMessage',
    description: 'Сообщение',
    type: 'string',
    example: 'delete'
)]
#[OA\Schema(
    schema: 'responseTotal',
    description: 'Всего найдено',
    type: 'integer',
    example: 1
)]
#[OA\Components(
    responses: [
        new OA\Response(
            response: 'telegramClientCreateResponse',
            description: 'Создание telegram клиента',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/telegramClient',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),

        new OA\Response(
            response: 'studentsIndexResponse',
            description: 'Список студентов',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/student', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'studentShowResponse',
            description: 'Информация по студенту',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/student',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'studentCreateResponse',
            description: 'Создание студента',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/student',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),

        new OA\Response(
            response: 'expertsIndexResponse',
            description: 'Список экспертов',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/expert', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'expertShowResponse',
            description: 'Информация по эксперту',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/expert',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'expertCreateResponse',
            description: 'Создание эксперта',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/expert',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),

        new OA\Response(
            response: 'expertPaymentStatusesIndexResponse',
            description: 'Список статусов выплат эксперту',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/expertPaymentStatus', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'expertPaymentsIndexResponse',
            description: 'Список выплат эксперту',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/expertPayment', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'expertPaymentCreateResponse',
            description: 'Создание эксперта',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/expertPayment',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),

        new OA\Response(
            response: 'placesIndexResponse',
            description: 'Список площадок',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/place', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'placeCreateResponse',
            description: 'Создание площадки',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/place',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'specializationsIndexResponse',
            description: 'Список специализаций',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            description: 'Результат',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/specialization', type: 'object')
                        ),
                        new OA\Property(property: 'total', ref: '#/components/schemas/responseTotal'),
                        new OA\Property(property: 'message', ref: '#/components/schemas/indexResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'specializationCreateResponse',
            description: 'Создание специализации',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/specialization',
                            description: 'Результат',
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', ref: '#/components/schemas/createdResponseMessage'),
                    ]
                )
            )
        ),

        new OA\Response(
            response: 'updateResponse',
            description: 'Результат обновления',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'message', ref: '#/components/schemas/updateResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'deleteResponse',
            description: 'Результат удаления',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'message', ref: '#/components/schemas/deleteResponseMessage'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'serverError',
            description: 'Ошибка обработки запроса',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'error', description: 'Ошибка', properties: [
                            new OA\Property(property: 'code', description: 'Код ошибки', type: 'number'),
                            new OA\Property(property: 'message', description: 'Сообщение ошибки', type: 'string'),
                            new OA\Property(property: 'data', description: 'Произвольный объект с пояснением к ошибке', type: 'object'),
                        ], type: 'object'
                        ),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'authLoginResponse',
            description: 'Вход',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'access_token', description: 'Токен', type: 'string'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'authMeResponse',
            description: 'Я',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'user_id', description: 'Идентификтор пользователя', type: 'integer'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'authLogoutResponse',
            description: 'Выход',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'message', description: 'Сообщение', type: 'string', example: 'Successfully logged out'),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 'authRefreshResponse',
            description: 'Обновить',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'access_token', description: 'Токен', type: 'string'),
                        new OA\Property(property: 'token_type', description: 'Тип токена', type: 'string'),
                        new OA\Property(property: 'expires_in', description: 'Время жизни токена', type: 'integer'),
                    ]
                )
            )
        ),

    ],

    requestBodies: [
        new OA\RequestBody(
            request: 'telegramClientCreateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['telegram_id',],
                        properties: [
                            new OA\Property(property: 'telegram_id', description: 'Идентификатор нашего клиента в telegram', type: 'integer', format: 'integer', example: 296112233),
                            new OA\Property(property: 'first_name', description: 'Имя в telegram', type: 'string', format: 'string', nullable: true),
                            new OA\Property(property: 'last_name', description: 'Фамилия в telegram', type: 'string', format: 'string', nullable: true),
                            new OA\Property(property: 'username', description: 'Имя пользователя в telegram', type: 'string', format: 'string', nullable: true),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'studentCreateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['first_name', 'telegram_client_id',],
                        properties: [
                            new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Шурик"),
                            new OA\Property(property: 'telegram_client_id', description: 'Идентификатор нашего клиента в telegram', type: 'integer', format: 'integer', example: 1),
                            new OA\Property(property: 'contact_phone_number', description: 'Номер телефона для контакта', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112244", nullable: true),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'studentUpdateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Владимир", nullable: true),
                            new OA\Property(property: 'contact_phone_number', description: 'Номер телефона для контакта', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112244", nullable: true),
                            new OA\Property(property: 'is_blocker', description: 'является ли блокированным', type: 'bool', format: 'bool', nullable: true),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'expertCreateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['first_name', 'last_name', 'patronymic', 'biography', 'telegram_client_id', 'telegram_phone_number', 'price_work_hour', 'requisites',],
                        properties: [
                            new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Владимир"),
                            new OA\Property(property: 'last_name', description: 'Фамилия', type: 'string', format: 'string', example: "Тотсамый"),
                            new OA\Property(property: 'patronymic', description: 'Отчество', type: 'string', format: 'string', example: "Владимирович"),
                            new OA\Property(property: 'biography', description: 'Биография', type: 'string', format: 'string', example: "Царь, очень приятно, царь"),
                            new OA\Property(property: 'telegram_client_id', description: 'Идентификатор нашего клиента в telegram', type: 'integer', format: 'integer', example: 1),
                            new OA\Property(property: 'telegram_phone_number', description: 'Номер телефона в telegram', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112233"),
                            new OA\Property(property: 'whatsapp_phone_number', description: 'Номер телефона в what\'s app', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112233", nullable: true),
                            new OA\Property(property: 'price_work_hour', description: 'Стоимость часа работы', type: 'float', format: 'float', example: 999.99),
                            new OA\Property(property: 'requisites', description: 'Реквизиты', type: 'string', format: 'string', example: "номер карты: 1111-4444-5555-9999"),
                            new OA\Property(property: 'avatar', description: 'Аватарка', type: 'file', format: 'image'),
                            new OA\Property(property: 'video', description: 'Видео', type: 'file', format: 'video'),
                            new OA\Property(
                                property: 'place_ids',
                                description: 'Список идентификаторов площадок',
                                type: 'array',
                                items: new OA\Items(type: "integer", format: "integer")
                            ),
                            new OA\Property(
                                property: 'specialization_ids',
                                description: 'Список идентификаторов специализаций',
                                type: 'array',
                                items: new OA\Items(type: "integer", format: "integer")
                            ),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'expertUpdateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'first_name', description: 'Имя', type: 'string', format: 'string', example: "Владимир", nullable: true),
                            new OA\Property(property: 'last_name', description: 'Фамилия', type: 'string', format: 'string', example: "Тотсамый", nullable: true),
                            new OA\Property(property: 'patronymic', description: 'Отчество', type: 'string', format: 'string', example: "Владимирович", nullable: true),
                            new OA\Property(property: 'biography', description: 'Биография', type: 'string', format: 'string', example: "Царь, очень приятно, царь", nullable: true),
                            new OA\Property(property: 'telegram_client_id', description: 'Идентификатор нашего клиента в telegram', type: 'integer', format: 'integer', nullable: true),
                            new OA\Property(property: 'telegram_phone_number', description: 'Номер телефона в telegram', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112233", nullable: true),
                            new OA\Property(property: 'whatsapp_phone_number', description: 'Номер телефона в what\'s app', type: 'string', format: 'string', maxLength: 11, minLength: 11, example: "79001112233", nullable: true),
                            new OA\Property(property: 'price_work_hour', description: 'Стоимость часа работы', type: 'float', format: 'float', example: 999.99, nullable: true),
                            new OA\Property(property: 'requisites', description: 'Реквизиты', type: 'string', format: 'string', example: "номер карты: 1111-4444-5555-9999", nullable: true),
                            new OA\Property(property: 'balance', description: 'Баланс', type: 'float', format: 'float', example: 5000.99, nullable: true),
                            new OA\Property(property: 'is_verification', description: 'является ли верифицированным', type: 'bool', format: 'bool', nullable: true),
                            new OA\Property(property: 'is_blocker', description: 'является ли блокированным', type: 'bool', format: 'bool', nullable: true),
                            new OA\Property(property: 'avatar', description: 'Аватарка', type: 'file', format: 'image', nullable: true),
                            new OA\Property(property: 'video', description: 'Видео', type: 'file', format: 'video', nullable: true),
                            new OA\Property(
                                property: 'place_ids',
                                description: 'Список идентификаторов площадок',
                                type: 'array',
                                items: new OA\Items(type: "integer", format: "integer"),
                                nullable: true
                            ),
                            new OA\Property(
                                property: 'specialization_ids',
                                description: 'Список идентификаторов специализаций',
                                type: 'array',
                                items: new OA\Items(type: "integer", format: "integer"),
                                nullable: true
                            ),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'expertPaymentUpdateRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['status_id'],
                        properties: [
                            new OA\Property(property: 'status_id', description: 'Идентификатор статуса выплаты эксперту', type: 'integer', format: 'integer'),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'placeRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['name'],
                        properties: [
                            new OA\Property(property: 'name', description: 'Название площадки', type: 'string', format: 'string'),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'specializationRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['name'],
                        properties: [
                            new OA\Property(property: 'name', description: 'Название специализации', type: 'string', format: 'string'),
                        ]
                    )
                )
            ]
        ),
        new OA\RequestBody(
            request: 'authLoginRequest',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['login', 'password', ],
                        properties: [
                            new OA\Property(property: 'login', description: 'Логин', type: 'string', format: 'string', example: 'root'),
                            new OA\Property(property: 'password', description: 'Пароль', type: 'string', format: 'string', example: 'qwe123123'),
                        ]
                    )
                )
            ]
        )
    ]
)]
abstract class ClientApiController extends Controller
{
}
