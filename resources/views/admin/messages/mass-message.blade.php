<x-admin-layout>
    @section('title', 'Wyślij masową wiadomość')
    @section('header', 'Wyślij masową wiadomość')

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h3 class="text-lg font-semibold text-white">Wyślij wiadomość do wielu użytkowników</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.messages.mass.send') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Typ odbiorców -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz odbiorców:</label>
                    
                    <div class="space-y-3" x-data="{ recipientType: 'selected' }">
                        <!-- Opcje wyboru odbiorców -->
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="recipient_type" value="all" class="form-radio h-4 w-4 text-blue-600" x-model="recipientType">
                                <span class="ml-2 text-sm text-gray-700">Wszyscy użytkownicy</span>
                            </label>
                            
                            <label class="inline-flex items-center">
                                <input type="radio" name="recipient_type" value="selected" class="form-radio h-4 w-4 text-blue-600" x-model="recipientType">
                                <span class="ml-2 text-sm text-gray-700">Wybrani użytkownicy</span>
                            </label>
                            
                            <label class="inline-flex items-center">
                                <input type="radio" name="recipient_type" value="role" class="form-radio h-4 w-4 text-blue-600" x-model="recipientType">
                                <span class="ml-2 text-sm text-gray-700">Według roli</span>
                            </label>
                        </div>
                        
                        <!-- Lista użytkowników (gdy wybrano konkretnych) -->
                        <div x-show="recipientType === 'selected'" class="mt-2">
                            <label for="selected_users" class="block text-sm font-medium text-gray-700 mb-1">Wybierz użytkowników:</label>
                            <select name="selected_users[]" id="selected_users" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md h-48">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Przytrzymaj Ctrl (lub Command na Mac) aby wybrać wielu użytkowników.</p>
                        </div>
                        
                        <!-- Lista ról (gdy wybrano według roli) -->
                        <div x-show="recipientType === 'role'" class="mt-2">
                            <label for="selected_role" class="block text-sm font-medium text-gray-700 mb-1">Wybierz rolę:</label>
                            <select name="selected_role" id="selected_role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">-- Wybierz rolę --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    @error('recipient_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('selected_users')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('selected_role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Temat -->
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Temat wiadomości:</label>
                    <input type="text" name="subject" id="subject" required
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           value="{{ old('subject') }}">
                    @error('subject')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Treść -->
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Treść wiadomości:</label>
                    <textarea name="content" id="content" rows="8" required
                             class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Załączniki -->
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">Załączniki (opcjonalnie):</label>
                    <div class="flex items-center">
                        <label for="attachments" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm
</x-admin-layout> 