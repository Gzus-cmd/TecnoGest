<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
    <!-- Historial de Componentes -->
    <a href="{{ $componentesUrl }}" 
       target="_blank"
       class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 text-white group">
        <svg class="w-12 h-12 mb-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
        </svg>
        <h3 class="font-bold text-lg text-center">Historial de Componentes</h3>
        <p class="text-sm text-blue-100 text-center mt-2">Ver cambios y asignaciones</p>
    </a>

    <!-- Historial de Mantenimientos -->
    <a href="{{ $mantenimientosUrl }}" 
       target="_blank"
       class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 text-white group">
        <svg class="w-12 h-12 mb-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <h3 class="font-bold text-lg text-center">Historial de Mantenimientos</h3>
        <p class="text-sm text-amber-100 text-center mt-2">Ver reparaciones y servicios</p>
    </a>

    <!-- Historial de Traslados -->
    <a href="{{ $trasladosUrl }}" 
       target="_blank"
       class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 text-white group">
        <svg class="w-12 h-12 mb-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
        <h3 class="font-bold text-lg text-center">Historial de Traslados</h3>
        <p class="text-sm text-green-100 text-center mt-2">Ver movimientos de ubicación</p>
    </a>
</div>
