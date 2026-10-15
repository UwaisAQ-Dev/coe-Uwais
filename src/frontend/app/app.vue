<script setup>
const config = useRuntimeConfig()
// Pembolehubah untuk Carian, Susunan & Muka Surat
const search = ref('')
const sortBy = ref('created_at')
const order = ref('desc')
const page = ref(1)

// Pembolehubah untuk teks yang sedang ditaip
const searchInput = ref('')
// Pembolehubah carian sebenar yang dihantar ke API

// 1. Tarik data dari Laravel API berserta parameter carian
const { data: responseData, pending, error, refresh } = await useFetch('/products', {
  baseURL: config.public.apiBase,
  query: {
    search: search,
    sort_by: sortBy,
    order: order,
    page: page
  }
})

// Ambil senarai kategori dari API backend
const { data: categories } = await useFetch('http://localhost:8000/api/categories')

const newProduct = ref({
  name: '',
  price: '',
  stock: '',
  description: '',
  category_id: '', 
})

newProduct.value = { name: '', price: '', stock: '', description: '', category_id: '' }

// Fungsi yang dijalankan bila tekan Enter atau klik Cari
const handleSearch = () => {
  search.value = searchInput.value
  page.value = 1 // Reset kembali ke muka surat 1 setiap kali carian baharu
}

const products = computed(() => responseData.value?.data || [])
const meta = computed(() => responseData.value?.meta || { current_page: 1, last_page: 1, total: 0 })

// 2. Pemboleh ubah borang reaktif (Form State)
const form = ref({
  name: '',
  price: '',
  stock: 10,
  description: '',
  image_url: '',
  category_id: '', 
})

const submitting = ref(false)
const formMessage = ref('')
const editingId = ref(null) // null = mod Tambah Baru, jika ada nombor ID = mod Edit

// 3. Fungsi menghantar data borang ke Laravel (POST /api/products)
async function submitProduct() {
  submitting.value = true
  formMessage.value = ''

    try {
    if (editingId.value) {
      // 🟢 1. JIKA SEDANG EDIT: Hantar arahan PUT ke URL dengan ID
      await $fetch(`${config.public.apiBase}/products/${editingId.value}`, {
        method: 'PUT',
        body: form.value
      })
      formMessage.value = '✅ Produk berjaya dikemaskini!'
      editingId.value = null // Kembali semula ke mod Tambah Baru
    } else {
      // 🔵 2. JIKA PRODUK BAHARU: Hantar arahan POST seperti biasa
      await $fetch('/products', {
        baseURL: config.public.apiBase,
        method: 'POST',
        body: form.value
      })
      formMessage.value = '✅ Produk berjaya ditambah!'
    }

    // 3. Kosongkan semula borang
    form.value = { name: '', price: '', stock: 10, description: '', image_url: '' }

    // 4. Muat semula senarai jadual secara automatik
    refresh()
  } catch (err) {
    console.error(err)
    formMessage.value = '❌ Gagal menyimpan produk. Sila semak input anda.'
  } finally {
    submitting.value = false
  }
 
}

const deleteProduct = async (id) => {
  // Tanya pengesahan pengguna terlebih dahulu
  if (!confirm('Adakah anda pasti mahu memadam produk ini?')) {
    return
  }

  try {
    // Hantar request HTTP DELETE ke endpoint: /api/products/{id}
    await $fetch(`${config.public.apiBase}/products/${id}`, {
      method: 'DELETE'
    })

    // Muat semula senarai produk dari API secara automatik
    refresh()
  } catch (err) {
    alert('Gagal memadam produk.')
  }

}

// 1. Fungsi apabila butang Edit ditekan pada mana-mana baris produk
const editProduct = (product) => {
  editingId.value = product.id
  
  // Masukkan data produk yang dipilih ke dalam kotak-kotak borang
  form.value = {
    name: product.name,
    price: product.price,
    stock: product.stock,
    description: product.description || '',
    image_url: product.image_url || '',
    category_id: product.category_id || '', 
  }

  // Skrol skrin ke atas secara lancar supaya pengguna nampak borang
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// 2. Fungsi jika pengguna mahu membatalkan suntingan
const cancelEdit = () => {
  editingId.value = null
  form.value = { name: '', price: '', stock: 10, description: '', image_url: '' , category_id: '' }
  formMessage.value = ''
}  

</script>

<template>
  <div style="padding: 30px; font-family: system-ui, sans-serif; max-width: 900px; margin: 0 auto;">
    <h1>🛍️ Katalog Produk COE (Milestone 1)</h1>
    <p style="color: #666;">Data ditarik secara live dari Laravel REST API (Port 8000)</p>

    <!-- BORANG TAMBAH PRODUK BAHARU -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #ddd;">
      <h2>{{ editingId ? '✏️ Kemaskini Produk' : '➕ Tambah Produk Baharu' }}</h2>
      
      <div v-if="formMessage" style="padding: 10px; margin-bottom: 15px; background: #e3f2fd; border-radius: 4px; font-weight: bold;">
        {{ formMessage }}
      </div>

      <form @submit.prevent="submitProduct" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
          <label>Nama Produk *</label><br />
          <input v-model="form.name" type="text" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="Contoh: Monitor Gaming" />
        </div>

        <div>
          <label>Harga (RM) *</label><br />
          <input v-model="form.price" type="number" step="0.01" min="0" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="0.00" />
        </div>

        <div>
          <label>Kategori</label><br />
          <select
            v-model="form.category_id"
            style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;"
            :style="{ color: !form.category_id ? '#757575' : '#000' }"
          >
            <!-- Placeholder Option -->
            <option value="" disabled selected>Pilih Kategori...</option>

            <!-- Senarai Kategori -->
            <option
              v-for="cat in categories"
              :key="cat.id"
              :value="cat.id"
              style="color: #000;"
            >
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label>Baki Stok *</label><br />
          <input v-model="form.stock" type="number" min="0" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="0" />
        </div>

        <div>
          <label>Pautan Gambar (URL)</label><br />
          <input v-model="form.image_url" type="url" style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="https://..." />
        </div>

        <div style="grid-column: span 2;">
          <label>Penerangan Produk</label><br />
          <textarea v-model="form.description" rows="2" style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="Penerangan ringkas..."></textarea>
        </div>

        <div style="grid-column: span 2;">
          <button type="submit" :disabled="submitting" style="background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer;">
            {{ submitting ? 'Menyimpan...' : '➕ Simpan Produk' }}
          </button>
          <!-- Butang Batal (hanya muncul jika sedang edit) -->
          <button v-if="editingId" type="button" @click="cancelEdit" style="margin-left: 10px; background-color: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
            Batal
          </button>
        </div>
      </form>
    </div>

    <!-- JADUAL PAPARAN PRODUK -->
    <div v-if="pending">⏳ Memuatkan data produk dari Laravel API...</div>
    <div v-else-if="error" style="color: red; padding: 15px; border: 1px solid red; border-radius: 8px;">
      ⚠️ Gagal menyambung ke Laravel API. Pastikan <code>php artisan serve</code> sedang berjalan di port 8000!
    </div>

    <div v-else>
    <!-- Bar Carian & Susunan -->
      <div style="display: flex; gap: 12px; margin-bottom: 16px; align-items: center; justify-content: space-between;">
        
        <!-- Kotak Carian Berserta Butang Cari -->
        <form @submit.prevent="handleSearch" style="display: flex; gap: 8px;">
          <input 
            v-model="searchInput" 
            placeholder="🔍 Cari nama atau penerangan produk..." 
            style="padding: 8px 12px; width: 300px; border: 1px solid #ccc; border-radius: 6px;"
          />
          <button 
            type="submit" 
            style="background-color: #4b5563; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;"
          >
            Cari
          </button>
        </form>

        <!-- Dropdown Susunan -->
        <div>
          <label style="font-weight: bold; margin-right: 8px;">Susun:</label>
          <select v-model="sortBy" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
            <option value="created_at">Terkini</option>
            <option value="price">Harga</option>
            <option value="name">Nama</option>
            <option value="stock">Baki Stok</option>
          </select>

          <select v-model="order" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-left: 6px;">
            <option value="asc">Menaik (A-Z / Murah)</option>
            <option value="desc">Menurun (Z-A / Mahal)</option>
          </select>
        </div>

      </div>
      <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; border-color: #ddd;">
        <thead>
          <tr style="background-color: #f8f9fa; text-align: left;">
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga (RM)</th>
            <th>Baki Stok</th>
            <th>Penerangan</th>
            <th>Tindakan</th> <!-- Tambah kolum ini -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>#{{ product.id }}</td>
            <td>
              <div><strong>{{ product.name }}</strong></div>
              <!-- Badge Kategori -->
              <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 10px; font-size: 12px; font-weight: 500;">
                {{ product.category?.name || 'Tiada Kategori' }}
              </span>
            </td>
                        <td style="color: #2e7d32; font-weight: bold;">RM {{ Number(product.price).toFixed(2) }}</td>
            <td>
              <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 13px; font-weight: bold;">
                {{ product.stock }} unit
              </span>
            </td>
            <td style="color: #555;">{{ product.description }}</td>
            <td>
                <!-- Butang Edit (Biru) -->
                <button 
                  @click="editProduct(product)" 
                  style="background-color: #2563eb; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-right: 6px;"
                >
                  Edit
                </button>
              
                <button 
                  @click="deleteProduct(product.id)" 
                  style="background-color: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;"
                >
                  Padam
                </button>
            </td>
          </tr>
        </tbody>
      </table>
          <!-- Butang Halaman (Pagination) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
      <span style="color: #666; font-size: 14px;">
        Halaman {{ meta.current_page }} daripada {{ meta.last_page }} (Jumlah: {{ meta.total }} produk)
      </span>

      <div>
        <button 
          :disabled="meta.current_page <= 1" 
          @click="page--"
          style="padding: 6px 14px; margin-right: 6px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;"
        >
          ◀ Sebelum
        </button>

        <button 
          :disabled="meta.current_page >= meta.last_page" 
          @click="page++"
          style="padding: 6px 14px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;"
        >
          Seterusnya ▶
        </button>
      </div>
    </div>
    </div>
  </div>
</template>