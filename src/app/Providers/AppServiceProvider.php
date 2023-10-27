<?php

namespace App\Providers;

use App\Repositories\ConsultationStorageRepository;
use App\Repositories\ConsultationStorageRepositoryInterface;
use App\Repositories\ExpertStorageRepository;
use App\Repositories\ExpertStorageRepositoryInterface;
use App\Repositories\Neo4jMenuStorageRepository;
use App\Repositories\Neo4jMenuStorageRepositoryInterface;
use App\Repositories\PlaceStorageRepository;
use App\Repositories\PlaceStorageRepositoryInterface;
use App\Repositories\SpecializationStorageRepository;
use App\Repositories\SpecializationStorageRepositoryInterface;
use App\Repositories\StudentStorageRepository;
use App\Repositories\StudentStorageRepositoryInterface;
use App\Repositories\TelegramStorageRepository;
use App\Repositories\TelegramStorageRepositoryInterface;
use App\Services\ConferenceService;
use App\Services\ConferenceServiceInterface;
use App\Services\DictionaryService;
use App\Services\DictionaryServiceInterface;
use App\Services\ExpertService;
use App\Services\ExpertServiceInterface;
use App\Services\StudentService;
use App\Services\StudentServiceInterface;
use App\Services\TelegramService;
use App\Services\TelegramServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface as Neo4jClientInterface;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientInterface::class, Client::class);

        $this->app->bind(
            PlaceStorageRepositoryInterface::class,
            function ($app) {
                return new PlaceStorageRepository(placeTableName: config('database.table_names.places'));
            }
        );
        $this->app->bind(
            SpecializationStorageRepositoryInterface::class,
            function ($app) {
                return new SpecializationStorageRepository(specializationTableName: config('database.table_names.specializations'));
            }
        );
        $this->app->bind(
            ExpertStorageRepositoryInterface::class,
            function ($app) {
                return new ExpertStorageRepository(
                    expertsTableName: config('database.table_names.experts'),
                    expertPlacesTableName: config('database.table_names.expert_places'),
                    expertSpecializationsTableName: config('database.table_names.expert_specializations'),
                    expertPaymentsTableName: config('database.table_names.expert_payments'),
                    expertPaymentStatusesTableName: config('database.table_names.expert_payment_statuses'),
                    placesTableName: config('database.table_names.places'),
                    specializationsTableName: config('database.table_names.specializations'),
                    telegramClient: config('database.table_names.telegram_clients')
                );
            }
        );
        $this->app->bind(
            StudentStorageRepositoryInterface::class,
            function ($app) {
                return new StudentStorageRepository(
                    studentsTableName: config('database.table_names.students'),
                    consultationsTableName: config('database.table_names.consultations'),
                    telegramClientsTableName: config('database.table_names.telegram_clients')
                );
            }
        );
        $this->app->bind(
            TelegramStorageRepositoryInterface::class,
            function ($app) {
                return new TelegramStorageRepository(
                    telegramClientsTableName: config('database.table_names.telegram_clients'),
                    telegramMenusTableName: config('database.table_names.telegram_menus'),
                    telegramMenuVersionsTableName: config('database.table_names.telegram_menu_versions'),
                    telegramMenuSessionsTableName: config('database.table_names.telegram_menu_sessions'),
                    telegramClientStepsTableName: config('database.table_names.telegram_client_steps'),
                    telegramClientStepMessagesTableName: config('database.table_names.telegram_client_step_messages'),
                    expertsTableName: config('database.table_names.experts'),
                    studentsTableName: config('database.table_names.students')
                );
            }
        );
        $this->app->bind(
            Neo4jMenuStorageRepositoryInterface::class,
            function ($app) {
                return new Neo4jMenuStorageRepository(
                    neo4jClient: $app->make(Neo4jClientInterface::class)
                );
            }
        );
        $this->app->bind(
            ConsultationStorageRepositoryInterface::class,
            function ($app) {
                return new ConsultationStorageRepository(
                    consultationsTableName: config('database.table_names.consultations'),
                    consultationStatusesTableName: config('database.table_names.consultation_statuses'),
                    consultationRequestsTableName: config('database.table_names.consultation_requests'),
                    expertConsultationRequestsTableName: config('database.table_names.expert_consultation_requests'),
                    consultationRequestStatusesTableName: config('database.table_names.consultation_request_statuses'),
                    expertConsultationRequestStatusesTableName: config('database.table_names.expert_consultation_request_statuses'),
                    studentsTableName: config('database.table_names.students'),
                    expertsTableName: config('database.table_names.experts'),
                    placesTableName: config('database.table_names.places'),
                    specializationsTableName: config('database.table_names.specializations'),
                    telegramClientsTableName: config('database.table_names.telegram_clients')
                );
            }
        );

        $this->app->bind(DictionaryServiceInterface::class, function ($app) {
            return new DictionaryService(
                placeStorageRepository: $app->make(PlaceStorageRepositoryInterface::class),
                specializationStorageRepository: $app->make(SpecializationStorageRepositoryInterface::class)
            );
        });
        $this->app->bind(ExpertServiceInterface::class, function ($app) {
            return new ExpertService(
                expertStorageRepository: $app->make(ExpertStorageRepositoryInterface::class),
                studentStorageRepository: $app->make(StudentStorageRepositoryInterface::class),
                telegramStorageRepository: $app->make(TelegramStorageRepositoryInterface::class)
            );
        });
        $this->app->bind(StudentServiceInterface::class, function ($app) {
            return new StudentService(
                studentStorageRepository: $app->make(StudentStorageRepositoryInterface::class),
                expertStorageRepository: $app->make(ExpertStorageRepositoryInterface::class),
                telegramStorageRepository: $app->make(TelegramStorageRepositoryInterface::class)
            );
        });
        $this->app->bind(ConferenceServiceInterface::class, function ($app) {
            return new ConferenceService(
                conferenceAppId: config('conference.hosting.jitsi.jitsi_app_id'),
                conferenceAppHost: config('conference.hosting.jitsi.jitsi_app_host'),
                conferenceAppSecretKey: config('conference.hosting.jitsi.jitsi_app_secret')
            );
        });
        $this->app->bind(TelegramServiceInterface::class, function ($app) {
            return new TelegramService(
                apiClient: new Api(config('telegram.bots.assist_seller.token')),
                neo4jMenuStorageRepository: $app->make(Neo4jMenuStorageRepositoryInterface::class),
                telegramStorageRepository: $app->make(TelegramStorageRepositoryInterface::class),
                expertStorageRepository: $app->make(ExpertStorageRepositoryInterface::class),
                studentStorageRepository: $app->make(StudentStorageRepositoryInterface::class),
                placeStorageRepository: $app->make(PlaceStorageRepositoryInterface::class),
                specializationStorageRepository: $app->make(SpecializationStorageRepositoryInterface::class),
                consultationStorageRepository: $app->make(ConsultationStorageRepositoryInterface::class),
                conferenceService: $app->make(ConferenceServiceInterface::class)
            );
        });

        $this->app->bind(Neo4jClientInterface::class, function ($app) {
            return ClientBuilder::create()
                ->withDriver('bolt', config('database.connections.neo4j.url'))
                ->withDefaultDriver('bolt')
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');
    }
}
