@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-zinc-100 p-4 lg:p-8 h-full min-h-0 overflow-y-auto grow">
        <div class="space-y-6 mx-auto w-full max-w-3xl">

            <div class="flex items-center gap-4">
                <a href="{{ route('professional.dashboard') }}"
                    class="hover:bg-zinc-200 -ml-2 p-2 rounded-lg text-zinc-500 hover:text-zinc-900 transition-colors"
                    title="Back to Dashboard">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="font-bold text-zinc-900 text-2xl">Profile Settings</h1>
            </div>

            @if(session('success'))
                <div class="bg-teal-50 p-4 border border-teal-200 rounded-xl font-medium text-teal-700">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Profile Form -->
            <div class="bg-white rounded-2xl overflow-hidden">
                <form action="{{ route('professional.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="p-6 sm:p-8 border-zinc-100 border-b">
                        <h2 class="mb-6 font-semibold text-zinc-800 text-lg">Personal Information</h2>

                        <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                            <!-- Avatar -->
                            <div class="flex items-center gap-6 col-span-1 md:col-span-2">
                                <div class="shrink-0">
                                    @if($professional->avatar)
                                        <img class="shadow-sm border-2 border-zinc-100 rounded-full w-24 h-24 object-cover"
                                            src="{{ Storage::url($professional->avatar) }}" alt="{{ $professional->name }}" />
                                    @else
                                        <div
                                            class="flex justify-center items-center bg-teal-100 shadow-sm border-2 border-teal-50 rounded-full w-24 h-24 font-bold text-teal-600 text-3xl">
                                            {{ substr($professional->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <label class="block">
                                        <span class="sr-only">Choose profile photo</span>
                                        <input type="file" name="avatar"
                                            class="block hover:file:bg-teal-100 file:bg-teal-50 file:mr-4 file:px-4 file:py-2.5 file:border-0 file:rounded-full w-full file:font-semibold text-zinc-500 file:text-teal-700 text-sm file:text-sm transition-colors cursor-pointer" />
                                    </label>
                                    <p class="mt-2 text-zinc-500 text-xs">JPG, JPEG, or PNG up to 2MB.</p>
                                </div>
                                @error('avatar') <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="name" class="block mb-2 font-medium text-zinc-700 text-sm">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $professional->name) }}"
                                    required
                                    class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                                @error('name') <span class="mt-1 text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>



                            <!-- Specialties -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="specialties" class="block mb-2 font-medium text-zinc-700 text-sm">Specialties
                                    <span class="font-normal text-zinc-400">(comma separated)</span></label>
                                <input type="text" name="specialties" id="specialties"
                                    value="{{ old('specialties', is_array($professional->specialties) ? implode(', ', $professional->specialties) : $professional->specialties) }}"
                                    placeholder="Depression, Anxiety, Family"
                                    class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                                @error('specialties') <span class="mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- WhatsApp Number -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="whatsapp_number" class="block mb-2 font-medium text-zinc-700 text-sm">WhatsApp
                                    Number</label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number"
                                    value="{{ old('whatsapp_number', $professional->whatsapp_number) }}"
                                    class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                                @error('whatsapp_number') <span class="mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-50/50 p-6 sm:p-8">
                        <h2 class="mb-6 font-semibold text-zinc-800 text-lg">Financial Information</h2>
                        <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block mb-2 font-medium text-zinc-700 text-sm">Bank
                                    Name</label>
                                <input type="text" name="bank_name" id="bank_name"
                                    value="{{ old('bank_name', $professional->bank_name) }}"
                                    placeholder="BCA / Mandiri / BNI"
                                    class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                                @error('bank_name') <span class="mt-1 text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Bank Account Number -->
                            <div>
                                <label for="bank_account_number"
                                    class="block mb-2 font-medium text-zinc-700 text-sm">Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                    value="{{ old('bank_account_number', $professional->bank_account_number) }}"
                                    class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                                @error('bank_account_number') <span class="mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end bg-white px-6 sm:px-8 py-4 border-zinc-100 border-t">
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 active:bg-teal-800 shadow-sm px-8 py-2.5 rounded-xl font-semibold text-white transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change Section -->
            <div class="bg-white mb-8 rounded-2xl overflow-hidden" x-data="{
                                    current_password: '',
                                    new_password: '',
                                    new_password_confirmation: '',
                                    loading: false,
                                    successMessage: '',
                                    errorMessage: '',
                                    errors: {},
                                    submitPasswordChange() {
                                        this.loading = true;
                                        this.successMessage = '';
                                        this.errorMessage = '';
                                        this.errors = {};

                                        axios.post('{{ route('professional.change-password') }}', {
                                            current_password: this.current_password,
                                            new_password: this.new_password,
                                            new_password_confirmation: this.new_password_confirmation
                                        })
                                        .then(response => {
                                            this.successMessage = response.data.message;
                                            this.current_password = '';
                                            this.new_password = '';
                                            this.new_password_confirmation = '';
                                        })
                                        .catch(error => {
                                            if (error.response && error.response.status === 422) {
                                                if (error.response.data.errors) {
                                                    this.errors = error.response.data.errors;
                                                } else {
                                                    this.errorMessage = error.response.data.message;
                                                }
                                            } else {
                                                this.errorMessage = 'An error occurred while changing the password.';
                                            }
                                        })
                                        .finally(() => {
                                            this.loading = false;
                                        });
                                    }
                                }">
                <div class="p-6 sm:p-8">
                    <h2 class="mb-6 font-semibold text-zinc-800 text-lg">Change Password</h2>

                    <div x-show="successMessage" x-text="successMessage" style="display: none;"
                        class="bg-teal-50 mb-6 p-4 border border-teal-200 rounded-xl font-medium text-teal-700">
                    </div>
                    <div x-show="errorMessage" x-text="errorMessage" style="display: none;"
                        class="bg-red-50 mb-6 p-4 border border-red-200 rounded-xl font-medium text-red-700"></div>

                    <form @submit.prevent="submitPasswordChange" class="space-y-5">
                        <div>
                            <label for="current_password" class="block mb-2 font-medium text-zinc-700 text-sm">Current
                                Password</label>
                            <input type="password" x-model="current_password" id="current_password" required
                                class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                            <template x-if="errors.current_password">
                                <span class="block mt-1 text-red-500 text-sm" x-text="errors.current_password[0]"></span>
                            </template>
                        </div>

                        <div>
                            <label for="new_password" class="block mb-2 font-medium text-zinc-700 text-sm">New
                                Password</label>
                            <input type="password" x-model="new_password" id="new_password" required
                                class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                            <template x-if="errors.new_password">
                                <span class="block mt-1 text-red-500 text-sm" x-text="errors.new_password[0]"></span>
                            </template>
                        </div>

                        <div>
                            <label for="new_password_confirmation"
                                class="block mb-2 font-medium text-zinc-700 text-sm">Confirm New Password</label>
                            <input type="password" x-model="new_password_confirmation" id="new_password_confirmation"
                                required
                                class="shadow-sm border-zinc-300 focus:border-teal-500 rounded-xl focus:ring-teal-500 w-full transition-shadow" />
                            <template x-if="errors.new_password_confirmation">
                                <span class="block mt-1 text-red-500 text-sm"
                                    x-text="errors.new_password_confirmation[0]"></span>
                            </template>
                        </div>

                        <div class="flex justify-end pt-4 w-full">
                            <button type="submit" :disabled="loading"
                                class="flex justify-center items-center bg-zinc-800 hover:bg-zinc-900 active:bg-zinc-950 disabled:opacity-70 shadow-sm px-8 py-2.5 rounded-xl font-semibold text-white transition-all disabled:cursor-not-allowed">
                                <span x-show="!loading">Update Password</span>
                                <span x-show="loading" style="display: none;" class="flex items-center">
                                    <svg class="mr-2 -ml-1 w-4 h-4 text-white animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection