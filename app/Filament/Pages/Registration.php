<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
// use Filament\Pages\Auth\Register;
// use Filament\Panels\Pages\Auth\Register;
use Filament\Auth\Pages\Register;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Schemas\Components\Component;
// use Filament\Schemas\Components\TextInput;
// use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;

class Registration extends Register
{
    // protected ?string $maxWidth = '2xl';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Wizard::make([
                Wizard\Step::make('Email')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        $this->getEmailFormComponent(),
                    ]),

                Wizard\Step::make('Profil')
                    ->icon('heroicon-o-user')
                    ->schema([
                        $this->getFirstNameFormComponent(),
                        $this->getLastNameFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getPhoneFormComponent(),
                    ]),

                Wizard\Step::make('Organizație')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        $this->getTeamNameFormComponent(),
                    ]),
            ])
            ->skippable(false)
            ->submitAction(
                new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="lg"
                        color="success"
                        class="w-full"
                    >
                        Finalizează înregistrarea
                    </x-filament::button>
                BLADE))
            ),
        ]);
    }


    protected function getFormActions(): array
    {
        return [];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->email()
            ->required()
            ->unique($this->getUserModel())
            ->label('Adresa de email')
            ->placeholder('ex: nume@domeniu.ro')
            ->autofocus()
            ->prefixIcon('heroicon-o-envelope');
    }

    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('first_name')
            ->label('Prenume')
            ->required()
            ->maxLength(255)
            ->prefixIcon('heroicon-o-user')
            ->placeholder('Ion');
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->label('Nume')
            ->required()
            ->maxLength(255)
            ->prefixIcon('heroicon-o-user')
            ->placeholder('Popescu');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->password()
            ->confirmed()
            ->required()
            ->minLength(8)
            ->label('Parola')
            ->revealable()
            ->prefixIcon('heroicon-o-lock-closed')
            ->helperText('Minim 8 caractere');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->password()
            ->required()
            ->label('Confirmă parola')
            ->revealable()
            ->prefixIcon('heroicon-o-lock-closed')
            ->dehydrated(false);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Număr de telefon')
            ->tel()
            ->placeholder('+407xxxxxxxx')
            ->prefixIcon('heroicon-o-phone');
    }

    protected function getTeamNameFormComponent(): Component
    {
        return TextInput::make('team_name')
            ->label('Numele organizației')
            ->required()
            ->maxLength(255)
            ->placeholder('Ex: Compania Mea SRL')
            ->prefixIcon('heroicon-o-building-office-2')
            ->helperText('Poți invita alți membri în organizație după ce finalizezi configurarea. Facturarea se face per loc utilizator.');
    }

    protected function handleRegistration(array $data): Model
    {
        $team = Team::create([
            'name' => $data['team_name'],
        ]);

        $user = $this->getUserModel()::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'name'       => $data['first_name'] . ' ' . $data['last_name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'phone'      => $data['phone'] ?? null,
            'team_id'    => $team->id,
        ]);

        $this->sendEmailVerificationNotification($user);
        $this->fillInvitedUserAndLogin($user);

        return $user;
    }
}