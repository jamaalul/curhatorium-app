<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Pengelolaan E-Class</x-slot>

        <p class="text-gray-600 dark:text-gray-400">
            Kelola module, materi chapter, quiz, entitlement, dan sertifikat Curhatorium Class.
        </p>

        <div class="mt-4">
            <x-filament::button tag="a" href="{{ url('/admin/cbt-modules') }}" icon="heroicon-m-book-open">
                Kelola Modul E-Class
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>
