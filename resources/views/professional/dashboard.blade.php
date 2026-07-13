@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-white lg:bg-zinc-100 p-0 lg:py-4 lg:pr-4 h-full min-h-0 grow"
         x-data="{ 
            deleteModalOpen: false, 
            slotToDelete: null,
            isDeleting: false,
            openDeleteModal(event) {
                this.slotToDelete = event.detail.id;
                this.deleteModalOpen = true;
            },
            deleteSlot() {
                if (!this.slotToDelete) return;
                this.isDeleting = true;
                
                // Construct URL
                let url = `/professional/slots/${this.slotToDelete}`;
                
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.isDeleting = false;
                    if (data.success) {
                        if (window._currentCalendarEvent) {
                            window._currentCalendarEvent.remove();
                            window._currentCalendarEvent = null;
                        }
                        this.deleteModalOpen = false;
                    } else {
                        alert(data.message || 'Gagal menghapus slot.');
                    }
                })
                .catch(err => {
                    this.isDeleting = false;
                    alert('Terjadi kesalahan saat menghapus slot.');
                    console.error(err);
                });
            }
         }"
         @open-delete-slot-modal.window="openDeleteModal($event)">
         
        <x-calendar class="flex-1 min-h-0" :events-url="route('api.professionals.schedule', $professional)" />
        
        <!-- Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="deleteModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         @click.away="deleteModalOpen = false"
                         class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Hapus Slot Jadwal</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus slot jadwal ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" @click="deleteSlot" :disabled="isDeleting" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto disabled:opacity-50">
                                <span x-show="!isDeleting">Hapus</span>
                                <span x-show="isDeleting">Menghapus...</span>
                            </button>
                            <button type="button" @click="deleteModalOpen = false" :disabled="isDeleting" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto disabled:opacity-50">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection