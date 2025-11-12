@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Order Stok</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="search-bar bg-white p-4 rounded-lg shadow mb-4">
        <div class="flex justify-between items-center">
            <div class="flex-1 mr-4">
                <form action="{{ route('order.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Ketik nama/kode..." 
                           class="w-full p-2 border rounded" 
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded ml-2">Cari</button>
                </form>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('order.status') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Status Pesanan</a>
                <button type="submit" form="orderForm" class="bg-blue-500 text-white px-4 py-2 rounded">Order</button>
            </div>
        </div>
    </div>

    @php
        $userRole = strtolower(session('role') ?? '');
        $isGudang = in_array($userRole, ['penjaga gudang', 'pejaga gudang']);
        $isPerencanaan = $userRole === 'perencanaan';
    @endphp

    @if(!$isGudang)
    {{-- Filter Tipe Rekap hanya untuk Perencanaan dan Admin, bukan untuk Gudang --}}
    <div class="bg-white p-4 rounded-lg shadow mb-4">
        <label for="tipe_rekap" class="block text-sm font-medium text-gray-700 mb-2">
            Tipe Rekap <span class="text-red-500">*</span>
        </label>
        <select name="tipe_rekap" id="tipe_rekap" form="orderForm" required
                class="w-full md:w-64 p-2 border rounded @error('tipe_rekap') border-red-500 @enderror">
            <option value="">-- Pilih Tipe Rekap --</option>
            <option value="sr" {{ old('tipe_rekap') == 'sr' ? 'selected' : '' }}>SR (Sales Representative)</option>
            <option value="gm" {{ old('tipe_rekap') == 'gm' ? 'selected' : '' }}>GM (General Manager)</option>
        </select>
        <input type="hidden" id="savedTipeRekap" value="{{ old('tipe_rekap') }}">
        @error('tipe_rekap')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
        <p class="text-gray-500 text-xs mt-1">Pilih tipe rekap untuk menentukan data masuk ke tabel SR atau GM</p>
    </div>
    @else
    {{-- Untuk Gudang, tipe_rekap otomatis di-set (hidden) --}}
    <input type="hidden" name="tipe_rekap" id="tipe_rekap" form="orderForm" value="sr">
    @endif

    <div class="bg-white rounded-lg shadow">
        <form id="orderForm" action="{{ route('order.store') }}" method="POST">
            @csrf
            <table class="min-w-full">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">No Kode</th>
                        <th class="py-3 px-4 text-left">Nama Barang</th>
                        <th class="py-3 px-4 text-left">Satuan</th>
                        <th class="py-3 px-4 text-left">Stok</th>
                        <th class="py-3 px-4 text-left">Sisa</th>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $item->kode_barang }}</td>
                        <td class="py-3 px-4">{{ $item->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $item->satuan }}</td>
                        <td class="py-3 px-4">{{ $item->stok }}</td>
                        <td class="py-3 px-4">{{ $item->sisa }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <input type="hidden" name="barang_id[]" value="{{ $item->kode_barang }}">
                                <button type="button" class="decrease-qty bg-gray-200 px-3 py-1 rounded" onclick="decreaseQuantity(this)">-</button>
                                <input type="number" name="quantity[]" value="0" min="0" 
                                       @if(!$isGudang) max="{{ $item->stok }}" @endif
                                       class="w-16 text-center border rounded py-1 px-2 quantity-input"
                                       data-stok="{{ $item->stok }}"
                                       data-kode="{{ $item->kode_barang }}"
                                       data-nama="{{ $item->nama_barang }}"
                                       data-is-gudang="{{ $isGudang ? '1' : '0' }}"
                                       id="qty-{{ $item->kode_barang }}">
                                <button type="button" class="increase-qty bg-gray-200 px-3 py-1 rounded" onclick="increaseQuantity(this)">+</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Key untuk localStorage
const STORAGE_KEY = 'order_quantities';
const STORAGE_TIPE_KEY = 'order_tipe_rekap';

// Fungsi untuk menyimpan quantity ke localStorage
// Fungsi ini akan MERGE dengan quantity yang sudah ada di localStorage
function saveQuantities() {
    // Ambil quantity yang sudah ada di localStorage (untuk barang yang tidak terlihat)
    let existingQuantities = {};
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            existingQuantities = JSON.parse(saved);
        }
    } catch (e) {
        console.error('Error reading existing quantities:', e);
    }
    
    // Ambil quantity dari input yang terlihat di halaman saat ini
    const currentQuantities = {};
    const inputs = document.querySelectorAll('.quantity-input');
    
    inputs.forEach(function(input) {
        const kode = input.getAttribute('data-kode');
        const value = parseInt(input.value) || 0;
        if (value > 0) {
            currentQuantities[kode] = value;
        } else {
            // Jika value = 0, hapus dari existing (jika ada)
            delete existingQuantities[kode];
        }
    });
    
    // Merge: gabungkan existing (barang yang tidak terlihat) dengan current (barang yang terlihat)
    const mergedQuantities = { ...existingQuantities, ...currentQuantities };
    
    // Simpan ke localStorage
    localStorage.setItem(STORAGE_KEY, JSON.stringify(mergedQuantities));
    
    console.log('Quantity disimpan:', mergedQuantities);
}

// Fungsi untuk restore quantity dari localStorage
function restoreQuantities() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const quantities = JSON.parse(saved);
            let restoredCount = 0;
            
            Object.keys(quantities).forEach(function(kode) {
                const input = document.getElementById('qty-' + kode);
                if (input) {
                    const savedValue = quantities[kode];
                    const maxValue = parseInt(input.getAttribute('max')) || 0;
                    // Pastikan value tidak melebihi max
                    const finalValue = Math.min(savedValue, maxValue);
                    input.value = finalValue;
                    restoredCount++;
                    console.log('Quantity di-restore untuk kode', kode, ':', finalValue);
                }
            });
            
            console.log('Total quantity yang di-restore:', restoredCount, 'dari', Object.keys(quantities).length, 'barang di localStorage');
        }
    } catch (e) {
        console.error('Error restoring quantities:', e);
    }
}

// Fungsi untuk menyimpan tipe rekap
function saveTipeRekap() {
    const tipeRekapElement = document.getElementById('tipe_rekap');
    if (tipeRekapElement) {
        const tipeRekap = tipeRekapElement.value;
    if (tipeRekap) {
        localStorage.setItem(STORAGE_TIPE_KEY, tipeRekap);
        }
    }
}

// Fungsi untuk restore tipe rekap
function restoreTipeRekap() {
    try {
        const saved = localStorage.getItem(STORAGE_TIPE_KEY);
        if (saved) {
            const select = document.getElementById('tipe_rekap');
            if (select && select.tagName === 'SELECT' && !select.value) {
                select.value = saved;
            }
        }
    } catch (e) {
        console.error('Error restoring tipe rekap:', e);
    }
}

// Fungsi untuk clear localStorage setelah order berhasil
function clearOrderStorage() {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(STORAGE_TIPE_KEY);
}

function decreaseQuantity(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    if (input.value > 0) {
        input.value = parseInt(input.value) - 1;
        saveQuantities(); // Simpan setiap perubahan
    }
}

function increaseQuantity(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    const isGudang = input.getAttribute('data-is-gudang') === '1';
    const max = input.hasAttribute('max') ? parseInt(input.getAttribute('max')) : null;
    const currentValue = parseInt(input.value) || 0;
    
    // Untuk Gudang (menambah stok), tidak ada batasan maksimal
    if (isGudang) {
        input.value = currentValue + 1;
        saveQuantities();
    } else {
        // Untuk Perencanaan (mengurangi stok), cek batasan stok
        if (max !== null && currentValue < max) {
            input.value = currentValue + 1;
            saveQuantities();
        } else if (max !== null) {
        alert('Stok tidak cukup. Stok tersedia: ' + max);
        } else {
            input.value = currentValue + 1;
            saveQuantities();
        }
    }
}

// Flag untuk mencegah duplikasi event listener
let eventListenersSetup = false;

// Fungsi untuk setup event listeners menggunakan event delegation
function setupEventListeners() {
    // Gunakan event delegation pada form untuk menghindari masalah dengan elemen yang berubah
    const orderForm = document.getElementById('orderForm');
    if (orderForm && !eventListenersSetup) {
        // Event delegation untuk quantity inputs
        orderForm.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                saveQuantities();
            }
        });
        
        orderForm.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                saveQuantities();
            }
        });
        
        orderForm.addEventListener('blur', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                saveQuantities();
            }
        }, true);
        
        eventListenersSetup = true;
    }
    
    // Simpan tipe rekap setiap kali berubah (hanya jika ada select element, bukan hidden input)
    const tipeRekapSelect = document.getElementById('tipe_rekap');
    if (tipeRekapSelect && tipeRekapSelect.tagName === 'SELECT') {
        // Hanya tambahkan sekali
        if (!tipeRekapSelect.hasAttribute('data-listener-added')) {
            tipeRekapSelect.setAttribute('data-listener-added', 'true');
            tipeRekapSelect.addEventListener('change', function() {
                saveTipeRekap();
            });
        }
    }
    
    // Simpan sebelum form search di-submit
    const searchForm = document.querySelector('.search-bar form');
    if (searchForm) {
        // Hanya tambahkan sekali
        if (!searchForm.hasAttribute('data-listener-added')) {
            searchForm.setAttribute('data-listener-added', 'true');
            searchForm.addEventListener('submit', function(e) {
                // Simpan semua quantity yang terlihat sebelum search
                saveQuantities();
                saveTipeRekap();
                console.log('Quantity disimpan sebelum search');
            });
        }
    }
}

// Event listener untuk perubahan quantity manual
document.addEventListener('DOMContentLoaded', function() {
    // Clear storage jika ada success message (order berhasil)
    const successMessage = document.querySelector('.bg-green-100');
    if (successMessage) {
        clearOrderStorage();
    }
    
    // Restore quantities dan tipe rekap saat halaman load
    restoreQuantities();
    restoreTipeRekap();
    
    // Setup event listeners
    setupEventListeners();
    
    // Setup ulang event listeners setelah sedikit delay (untuk memastikan DOM sudah siap)
    setTimeout(function() {
        setupEventListeners();
        // Restore lagi setelah delay (untuk memastikan semua input sudah ter-render)
        restoreQuantities();
    }, 100);
    
    // Juga setup saat window load (untuk memastikan)
    window.addEventListener('load', function() {
        setupEventListeners();
        restoreQuantities();
    });
});

// Fungsi untuk menambahkan hidden input untuk barang yang tidak terlihat di halaman
function addHiddenOrderInputs() {
    // Hapus hidden input yang sudah ada sebelumnya (jika ada)
    const existingHidden = document.querySelectorAll('.hidden-order-input');
    existingHidden.forEach(function(el) {
        el.remove();
    });
    
    // Ambil semua quantity dari localStorage
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const quantities = JSON.parse(saved);
            const form = document.getElementById('orderForm');
            
            // Ambil SEMUA kode barang yang TERLIHAT di halaman (ada di tabel, terlepas dari quantity-nya)
            const visibleKodes = new Set(); // Gunakan Set untuk performa lebih baik
            const visibleInputs = document.querySelectorAll('.quantity-input');
            visibleInputs.forEach(function(input) {
                const kode = input.getAttribute('data-kode');
                visibleKodes.add(kode); // Tambahkan semua kode yang terlihat, tidak peduli quantity-nya
            });
            
            console.log('Kode barang yang terlihat di halaman:', Array.from(visibleKodes));
            console.log('Kode barang di localStorage:', Object.keys(quantities));
            
            // Untuk setiap barang di localStorage
            Object.keys(quantities).forEach(function(kode) {
                const localStorageQty = quantities[kode];
                const isVisible = visibleKodes.has(kode); // Cek apakah barang terlihat di halaman
                
                // Jika barang TIDAK terlihat di halaman TAPI ada quantity di localStorage
                if (localStorageQty > 0 && !isVisible) {
                    console.log('Menambahkan hidden input untuk barang yang tidak terlihat:', kode, 'Quantity:', localStorageQty);
                    
                    // Tambahkan hidden input untuk barang_id
                    const hiddenBarangId = document.createElement('input');
                    hiddenBarangId.type = 'hidden';
                    hiddenBarangId.name = 'barang_id[]';
                    hiddenBarangId.value = kode;
                    hiddenBarangId.className = 'hidden-order-input';
                    
                    // Tambahkan hidden input untuk quantity
                    const hiddenQuantity = document.createElement('input');
                    hiddenQuantity.type = 'hidden';
                    hiddenQuantity.name = 'quantity[]';
                    hiddenQuantity.value = localStorageQty;
                    hiddenQuantity.className = 'hidden-order-input';
                    
                    // Append ke form (pastikan urutan: barang_id dulu, lalu quantity)
                    form.appendChild(hiddenBarangId);
                    form.appendChild(hiddenQuantity);
                }
            });
            
            // Log jumlah hidden input yang ditambahkan
            const addedHidden = document.querySelectorAll('.hidden-order-input');
            console.log('Total hidden input yang ditambahkan:', addedHidden.length / 2, 'barang');
        }
    } catch (e) {
        console.error('Error adding hidden inputs:', e);
    }
}

// Validasi sebelum submit order
document.getElementById('orderForm').addEventListener('submit', function(e) {
    // Simpan quantity terlebih dahulu
    saveQuantities();
    
    // Tambahkan hidden input untuk barang yang tidak terlihat
    addHiddenOrderInputs();
    
    // Validasi semua input (termasuk yang terlihat dan hidden)
    const allQuantityInputs = document.querySelectorAll('input[name="quantity[]"]');
    const allBarangInputs = document.querySelectorAll('input[name="barang_id[]"]');
    
    // Debug: log jumlah input dan urutan
    console.log('Total barang_id:', allBarangInputs.length);
    console.log('Total quantity:', allQuantityInputs.length);
    
    // Log urutan array untuk debugging
    console.log('=== URUTAN ARRAY YANG AKAN DIKIRIM ===');
    for (let i = 0; i < Math.min(allBarangInputs.length, 10); i++) { // Log 10 pertama saja
        console.log(`Index ${i}: barang_id=${allBarangInputs[i].value}, quantity=${allQuantityInputs[i].value}`);
    }
    if (allBarangInputs.length > 10) {
        console.log(`... dan ${allBarangInputs.length - 10} item lainnya`);
    }
    console.log('=======================================');
    
    let hasError = false;
    let errorMessage = '';
    let hasQuantity = false;
    let quantitiesMap = {};

    // Buat mapping untuk validasi - pastikan jumlah barang_id dan quantity sama
    if (allBarangInputs.length !== allQuantityInputs.length) {
        console.error('Jumlah barang_id dan quantity tidak sama!', {
            barang_id: allBarangInputs.length,
            quantity: allQuantityInputs.length
        });
        e.preventDefault();
        alert('Terjadi kesalahan: Jumlah barang_id dan quantity tidak sesuai. Silakan refresh halaman dan coba lagi.');
        return false;
    }
    
    // Validasi dan hitung total quantity
    console.log('=== DAFTAR BARANG YANG AKAN DIORDER ===');
    for (let i = 0; i < allBarangInputs.length; i++) {
        const kode = allBarangInputs[i].value;
        const value = parseInt(allQuantityInputs[i].value) || 0;
        
        if (value > 0) {
            hasQuantity = true;
            quantitiesMap[kode] = value;
            console.log(`${i + 1}. Kode: ${kode}, Quantity: ${value}`);
        }
    }
    console.log('Total barang yang akan diorder:', Object.keys(quantitiesMap).length);
    console.log('========================================');

    // Validasi untuk input yang terlihat (hanya untuk Perencanaan, bukan Gudang)
    const visibleInputs = document.querySelectorAll('.quantity-input');
    visibleInputs.forEach(function(input) {
        const value = parseInt(input.value) || 0;
        const isGudang = input.getAttribute('data-is-gudang') === '1';
        const nama = input.getAttribute('data-nama');
        
        // Hanya validasi max untuk Perencanaan (yang mengurangi stok)
        // Gudang tidak perlu validasi max karena menambah stok
        if (!isGudang && input.hasAttribute('max')) {
            const max = parseInt(input.getAttribute('max')) || 0;
        if (value > max) {
            hasError = true;
            errorMessage += 'Stok tidak cukup untuk ' + nama + '. Stok tersedia: ' + max + ', dibutuhkan: ' + value + '\n';
            }
        }
    });

    if (hasError) {
        e.preventDefault();
        alert(errorMessage);
        return false;
    }
    
    if (!hasQuantity) {
        e.preventDefault();
        alert('Silakan pilih minimal satu barang dengan quantity lebih dari 0');
        return false;
    }
    
    // Jangan clear storage di sini, biarkan clear setelah order benar-benar berhasil
    // Storage akan di-clear jika ada success message
});
</script>
@endpush
@endsection