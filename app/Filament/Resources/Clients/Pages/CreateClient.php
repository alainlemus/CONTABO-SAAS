<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class CreateClient extends CreateRecord
{
    use HasWizard;

    protected static string $resource = ClientResource::class;

    /** @return array<Step> */
    protected function getSteps(): array
    {
        return ClientForm::wizardSteps();
    }

    public function getWizardComponent(): Component
    {
        return Section::make()
            ->schema([
                Wizard::make($this->getSteps())
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction($this->getSubmitFormAction())
                    ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
                    ->skippable($this->hasSkippableSteps())
                    ->contained(false),
            ]);
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->ownerId();

        return $data;
    }
}
