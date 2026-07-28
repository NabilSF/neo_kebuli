@props(['query' => ''])

<div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
    <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
    <p class="text-gray-500 max-w-md mx-auto mb-6">
        Kami tidak menemukan kode KBLI yang cocok dengan "{{ $query }}". Silakan periksa ejaan Anda atau coba kata kunci yang berbeda.
    </p>
    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        Kembali ke Beranda
    </a>
</div>
