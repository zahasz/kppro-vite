<input type="file" name="attachments[]" id="attachments" multiple 
        class="sr-only" onchange="updateFileList(this)">
<span id="selected-files" class="ml-3 text-sm text-gray-500">Nie wybrano plików</span>
</div>
<ul id="file-list" class="mt-2 space-y-1 text-sm text-gray-500"></ul>
@error('attachments')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
@error('attachments.*')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
</div>

<!-- Przyciski -->
<div class="flex justify-between">
    <a href="{{ route('messages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-arrow-left mr-2"></i> Wróć do wiadomości
    </a>
    
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-paper-plane mr-2"></i> Wyślij wiadomość
    </button>
</div>
</form>

<script>
function updateFileList(input) {
    const fileList = document.getElementById('file-list');
    const selectedFilesText = document.getElementById('selected-files');
    
    fileList.innerHTML = '';
    
    if (input.files.length > 0) {
        selectedFilesText.textContent = `Wybrano ${input.files.length} ${input.files.length === 1 ? 'plik' : 'pliki'}`;
        
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const li = document.createElement('li');
            li.className = 'flex items-center text-sm';
            
            const icon = document.createElement('i');
            icon.className = 'fas fa-file mr-2 text-blue-500';
            
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'ml-2 text-gray-400';
            fileSize.textContent = `(${formatFileSize(file.size)})`;
            
            li.appendChild(icon);
            li.appendChild(fileName);
            li.appendChild(fileSize);
            fileList.appendChild(li);
        }
    } else {
        selectedFilesText.textContent = 'Nie wybrano plików';
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    else return (bytes / 1048576).toFixed(1) + ' MB';
}
</script>
</div>
</x-app-layout> 