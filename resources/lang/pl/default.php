<?php

return [
    'user_menu_label' => 'Mój profil',
    'password_confirm' => [
        'heading' => 'Potwierdź hasło',
        'description' => 'Proszę potwierdzić swoje hasło, aby ukończyć tę akcję.',
        'current_password' => 'Aktualne hasło',
    ],
    'profile' => [
        'account' => 'Konto',
        'profile' => 'Profil',
        'my_profile' => 'Mój profil',
        'subheading' => 'Zarządzaj swoim profilem.',
        'personal_info' => [
            'heading' => 'Twoje dane osobowe',
            'subheading' => 'Edytuj swoje dane osobowe.',
            'submit' => [
                'label' => 'Zapisz',
            ],
            'notify' => 'Dane osobowe zostały zaktualizowane',
        ],
        'browser_sessions' => [
            'heading' => 'Sesje przeglądarki',
            'subheading' => 'Zarządzaj aktywnymi sesjami.',
            'label' => 'Sesje przeglądarki',
            'content' => 'Jeśli to konieczne, możesz wylogować się ze wszystkich innych sesji przeglądarki na wszystkich swoich urządzeniach. Niektóre z Twoich ostatnich sesji są wymienione poniżej; jednak ta lista może nie być wyczerpująca. Jeśli uważasz, że Twoje konto zostało naruszone, powinieneś również zaktualizować swoje hasło.',
            'device' => 'To urządzenie',
            'last_active' => 'Ostatnio aktywny',
            'logout_other_sessions' => 'Wyloguj inne sesje przeglądarki',
            'logout_heading' => 'Wyloguj inne sesje przeglądarki',
            'logout_description' => 'Proszę wprowadzić swoje hasło, aby potwierdzić, że chcesz wylogować się z innych sesji przeglądarki na wszystkich swoich urządzeniach.',
            'logout_action' => 'Wyloguj inne sesje przeglądarki',
            'incorrect_password' => 'Wprowadzone hasło jest nieprawidłowe. Proszę spróbować ponownie.',
            'logout_success' => 'Wszystkie inne sesje przeglądarki zostały pomyślnie wylogowane.',
        ],
        'password' => [
            'heading' => 'Hasło',
            'subheading' => 'Hasło powinno składać się przynajmniej 8 znaków.',
            'submit' => [
                'label' => 'Zapisz',
            ],
            'notify' => 'Hasło zostało zaktualizowane',
        ],
        'sanctum' => [
            'title' => 'Tokeny API',
            'description' => 'Zarządzaj tokenami API, które pozwalają aplikacjom zewnętrznym uzyskać dostęp do Twoich danych.',
            'create' => [
                'notify' => 'Token został utworzony',
                'message' => 'Token jest widoczny tylko raz podczas tworzenia. Jeśli zgubisz token, będziesz musiał go usunąć i utworzyć nowy.',
                'submit' => [
                    'label' => 'Stwórz token',
                ],
            ],
            'update' => [
                'notify' => 'Token zaktualizowany pomyślnie!',
                'submit' => [
                    'label' => 'Aktualizuj token',
                ],
            ],
            'copied' => [
                'label' => 'Token został skopiowany',
            ],
        ],
        'passkeys' => [
            'title' => 'Klucze dostępu',
            'description' => 'Klucze dostępu umożliwiają bezpieczne logowanie bez hasła.',
            'name' => 'Nazwa',
            'create' => 'Utwórz',
            'delete' => 'Usuń',
            'last_used' => 'Ostatnio użyty',
            'not_used_yet' => 'Jeszcze nie używany',
            'deleted_notification_title' => 'Klucz dostępu usunięty pomyślnie',
            'created_notification_title' => 'Klucz dostępu utworzony pomyślnie',
        ],
        'two_factor' => [
            'title' => 'Uwierzytelnianie dwuskładnikowe',
            'description' => 'Dodaj dodatkowe zabezpieczenie do swojego konta za pomocą uwierzytelniania dwuskładnikowego.',
        ],
    ],
    'clipboard' => [
        'link' => 'Kopiuj do schowka',
        'tooltip' => 'Skopiowano!',
    ],
    'fields' => [
        'avatar' => 'Awatar',
        'email' => 'Email',
        'login' => 'Login',
        'name' => 'Nazwa użytkownika',
        'password' => 'Hasło',
        'password_confirm' => 'Potwierdź hasło',
        'new_password' => 'Nowe hasło',
        'new_password_confirmation' => 'Potwierdź nowe hasło',
        'token_name' => 'Nazwa tokenu',
        'token_expiry' => 'Ważność tokenu',
        'abilities' => 'Uprawnienia',
        'created' => 'Utworzono',
        'expires' => 'Wygasa',
        'never_expires' => 'Nigdy nie wygasa',
    ],
    'or' => 'Lub',
    'cancel' => 'Anuluj',

];
