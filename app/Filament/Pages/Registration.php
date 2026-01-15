<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Auth\Pages\Register;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use App\Models\Team;
use App\Models\Industry;
use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class Registration extends Register
{
    public function getMaxWidth(): string
    {
        return '5xl'; 
    }

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

                Wizard\Step::make('Industrie')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        $this->getIndustryFormComponent(),
                    ]),

                Wizard\Step::make('Mărimea echipei')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        $this->getTeamSizeFormComponent(),
                    ]),

                Wizard\Step::make('Invită echipa')
                    ->icon('heroicon-o-users')
                    ->schema([
                        $this->getTeamInvitationsFormComponent(),
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

    protected function getIndustryFormComponent(): Component
    {
        return Radio::make('industry_ids')
            ->label('Alege 1 industrie în care activează compania ta')
            ->helperText('Vom personaliza CRM-ul în funcție de nevoile industriei tale')
            ->options(Industry::pluck('name', 'id')->toArray())
            ->required()
            ->inline(false)
            ->columns(2);
    }

    protected function getTeamSizeFormComponent(): Component
    {
        return Radio::make('team_size_range')
            ->label('Câte persoane vor folosi Leadsnap?')
            ->helperText('Acest lucru ne ajută să-ți oferim cel mai bun plan de prețuri')
            ->options([
                'doar-eu' => 'Doar eu',
                '2-4' => '2 - 4',
                '5-9' => '5 - 9',
                '10-24' => '10 - 24',
                '25-50' => '25 - 50',
                '51-100' => '51 - 100',
                'peste-100' => 'Peste 100',
            ])
            ->required()
            ->inline(false);
    }

    protected function getTeamInvitationsFormComponent(): Component
    {
        return Repeater::make('team_invitations')
            ->label('Invită-ți echipa')
            ->helperText('Adaugă colegii care vor folosi Leadsnap. Poți sări acest pas și să-i inviți mai târziu.')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Adresa de email')
                            ->email()
                            ->required()
                            ->placeholder('coleg@companie.ro')
                            ->prefixIcon('heroicon-o-envelope'),
                        
                        Select::make('role')
                            ->label('Rol')
                            ->options([
                                'user' => 'Utilizator',
                                'admin' => 'Admin',
                            ])
                            ->default('user')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('Utilizator: Poate accesa CRM-ul și gestiona contacte, deal-uri | Admin: Toate permisiunile + gestionare echipă și billing'),
                    ]),
            ])
            ->defaultItems(0)
            ->addActionLabel('+ Adaugă')
            ->reorderable(false)
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn (array $state): ?string => $state['email'] ?? null);
    }

    protected function handleRegistration(array $data): Model
    {
        $team = Team::create([
            'name' => $data['team_name'],
            'team_size_range' => $data['team_size_range'],
            'exact_team_size' => $data['exact_team_size'] ?? null,
        ]);

        // Attach industries to team
        if (!empty($data['industry_ids'])) {
            $team->industries()->attach($data['industry_ids']);
        }

        // Create user (owner is automatically admin)
        $user = $this->getUserModel()::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'name'       => $data['first_name'] . ' ' . $data['last_name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'phone'      => $data['phone'] ?? null,
            'team_id'    => $team->id,
        ]);

        // Assign admin role to the team owner
        $user->assignRole('admin');

        // Create team invitations
        if (!empty($data['team_invitations'])) {
            foreach ($data['team_invitations'] as $invitation) {
                TeamInvitation::create([
                    'team_id' => $team->id,
                    'email' => $invitation['email'],
                    'role' => $invitation['role'] ?? 'user',
                    'token' => Str::random(32), // unique token for invitation link
                ]);
            }

            // TODO: Send invitation emails
            // $this->sendTeamInvitations($team, $data['team_invitations']);
        }

        $this->sendEmailVerificationNotification($user);
        $this->fillInvitedUserAndLogin($user);

        return $user;
    }
}