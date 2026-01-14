<x-filament::button type="submit" size="lg" class="w-full mt-6">
    {{ $this->getCurrentStep() === $this->getSteps()->count() ? 'Finalizează înregistrarea' : 'Înainte →' }}
</x-filament::button>