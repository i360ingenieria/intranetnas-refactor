<div class="p-4 bg-white shadow rounded">
    <form wire:submit.prevent="submit">
        <input type="text" wire:model="title" placeholder="Título"
               class="border p-2 w-full mb-2 rounded">
        <textarea wire:model="body" placeholder="Contenido"
                  class="border p-2 w-full mb-2 rounded"></textarea>
        <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded">
            Publicar
        </button>
    </form>
</div>
