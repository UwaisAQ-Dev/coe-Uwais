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
const { data: categories } = await useFetch('/categories', {
  baseURL: config.public.apiBase,
})

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
// Pembolehubah untuk simpan gambar mana yang diklik untuk dibesarkan
const previewImage = ref(null)

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
      formMessage.value = '✅ Product updated successfully!'
      editingId.value = null // Kembali semula ke mod Tambah Baru
    } else {
      // 🔵 2. JIKA PRODUK BAHARU: Hantar arahan POST seperti biasa
      await $fetch('/products', {
        baseURL: config.public.apiBase,
        method: 'POST',
        body: form.value
      })
      formMessage.value = '✅ Product created successfully!'
    }

    // 3. Kosongkan semula borang
    form.value = { name: '', price: '', stock: 10, description: '', image_url: '', category_id: '' }

    // 4. Muat semula senarai jadual secara automatik
    refresh()
  } catch (err) {
    console.error(err)
    formMessage.value = '❌ Failed to save product. Please check your inputs.'
  } finally {
    submitting.value = false
  }
 
}

const deleteProduct = async (id) => {
  // Tanya pengesahan pengguna terlebih dahulu
  if (!confirm('Are you sure you want to delete this product?')) {
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
    alert('Failed to delete product.')
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
  <div style="padding: 30px; font-family: system-ui, sans-serif; max-width: 1100px; margin: 0 auto;">
    <h1>🛍️ COE Product Catalog (Milestone 1)</h1>
   <!-- <p style="color: #666;">Live data fetched from Laravel REST API (Port 8000)</p> -->

    <!-- BORANG TAMBAH PRODUK BAHARU -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #ddd;">
      <h2>{{ editingId ? '✏️ Edit Product' : '➕ Add New Product' }}</h2>
      
      <div v-if="formMessage" style="padding: 10px; margin-bottom: 15px; background: #e3f2fd; border-radius: 4px; font-weight: bold;">
        {{ formMessage }}
      </div>

      <form @submit.prevent="submitProduct" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 15px;">
        <div>
          <label>Product Name *</label><br />
          <input v-model="form.name" type="text" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="e.g. Pro Gaming Laptop 15" />
        </div>

        <div>
          <label>Price (RM) *</label><br />
          <input v-model="form.price" type="number" step="0.01" min="0" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="0.00" />
        </div>

        <div>
          <label>Category</label><br />
          <select
            v-model="form.category_id"
            style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;"
            :style="{ color: !form.category_id ? '#757575' : '#000' }"
          >
            <!-- Placeholder Option -->
            <option value="" disabled selected>Select Category...</option>

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
          <label>Stock Balance *</label><br />
          <input v-model="form.stock" type="number" min="0" required style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="0" />
        </div>

        <div>
          <label>Image URL</label><br />
          <input v-model="form.image_url" type="url" style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="https://..." />
          <div v-if="form.image_url" style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
            <img :src="form.image_url" alt="Preview" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;" @error="(e) => e.target.style.display = 'none'" />
            <span style="font-size: 12px; color: #666;">Preview</span>
          </div>
        </div>

        <div style="grid-column: span 2;">
          <label>Product Description</label><br />
          <textarea v-model="form.description" rows="2" style="width: 100%; padding: 8px; margin-top: 4px;" placeholder="Short product description..."></textarea>
        </div>

        <div style="grid-column: span 2;">
          <button type="submit" :disabled="submitting" style="background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer;">
            {{ submitting ? 'Saving...' : (editingId ? '💾 Update Product' : '➕ Save Product') }}
          </button>
          <!-- Butang Batal (hanya muncul jika sedang edit) -->
          <button v-if="editingId" type="button" @click="cancelEdit" style="margin-left: 10px; background-color: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- JADUAL PAPARAN PRODUK -->
    <div v-if="pending">⏳ Loading product data from Laravel API...</div>
    <div v-else-if="error" style="color: red; padding: 15px; border: 1px solid red; border-radius: 8px;">
      ⚠️ Failed to connect to Laravel API. Make sure the backend server is running on port 8000!
    </div>

    <div v-else>
    <!-- Bar Carian & Susunan -->
      <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; align-items: center; justify-content: space-between;">
        
        <!-- Kotak Carian Berserta Butang Cari -->
        <form @submit.prevent="handleSearch" style="display: flex; gap: 8px;">
          <input 
            v-model="searchInput" 
            placeholder="🔍 Search product name or description..." 
            style="padding: 8px 12px; width: 300px; border: 1px solid #ccc; border-radius: 6px;"
          />
          <button 
            type="submit" 
            style="background-color: #4b5563; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;"
          >
            Search
          </button>
        </form>

        <!-- Dropdown Susunan -->
        <div>
          <label style="font-weight: bold; margin-right: 8px;">Sort by:</label>
          <select v-model="sortBy" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
            <option value="created_at">Latest</option>
            <option value="price">Price</option>
            <option value="name">Product Name</option>
            <option value="stock">Stock</option>
          </select>

          <select v-model="order" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-left: 6px;">
            <option value="asc">Ascending (A-Z / Low to High)</option>
            <option value="desc">Descending (Z-A / High to Low)</option>
          </select>
        </div>

      </div>
      <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 8px; border: 1px solid #ddd;">
        <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; border: none;">
          <thead>
            <tr style="background-color: #f8f9fa; text-align: left;">
              <th>ID</th>
              <th style="width: 70px; text-align: center;">Image</th>
              <th>Product Name</th>
              <th>Price (RM)</th>
              <th>Stock</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>#{{ product.id }}</td>
              <td style="text-align: center; width: 80px;">
                <!-- Klik kotak ini untuk besarkan gambar -->
                <div 
                  @click="product.image_url ? previewImage = product : null"
                  :style="{ cursor: product.image_url ? 'zoom-in' : 'default' }"
                  title="Click to view full image"
                  style="width: 65px; height: 65px; border-radius: 8px; overflow: hidden; background: #f3f4f6; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; transition: transform 0.2s;"
                >
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    style="width: 100%; height: 100%; object-fit: cover;"
                    @error="(e) => e.target.style.display = 'none'"
                  />
                  <span v-else style="font-size: 24px;">📦</span>
                </div>
              </td>
              <td>
                <div style="font-weight: 600; font-size: 15px;">{{ product.name }}</div>
                <!-- Badge Kategori -->
                <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 10px; font-size: 12px; font-weight: 500;">
                  {{ product.category?.name || 'Uncategorized' }}
                </span>
              </td>
              <td style="color: #2e7d32; font-weight: bold; white-space: nowrap;">RM {{ Number(product.price).toFixed(2) }}</td>
              <td style="white-space: nowrap;">
                <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 13px; font-weight: bold;">
                  {{ product.stock }} units
                </span>
              </td>
              <td style="color: #555; font-size: 14px;">{{ product.description }}</td>
              <td style="white-space: nowrap;">
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
                    Delete
                  </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>  
          <!-- Butang Halaman (Pagination) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
      <span style="color: #666; font-size: 14px;">
        Page {{ meta.current_page }} of {{ meta.last_page }} (Total: {{ meta.total }} products)
      </span>

      <div>
        <button 
          :disabled="meta.current_page <= 1" 
          @click="page--"
          style="padding: 6px 14px; margin-right: 6px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;"
        >
          ◀ Previous
        </button>

        <button 
          :disabled="meta.current_page >= meta.last_page" 
          @click="page++"
          style="padding: 6px 14px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;"
        >
          Next ▶
        </button>
      </div>
    </div>
    </div>
        <!-- TETINGKAP POP-UP GAMBAR BESAR (LIGHTBOX MODAL) -->
    <div 
      v-if="previewImage" 
      @click="previewImage = null" 
      style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.75); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; box-sizing: border-box;"
    >
      <div 
        @click.stop 
        style="background: white; padding: 24px; border-radius: 12px; max-width: 520px; width: 100%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); position: relative;"
      >
        <!-- Butang Tutup (X) -->
        <button 
          @click="previewImage = null" 
          style="position: absolute; top: 12px; right: 12px; background: #f3f4f6; border: none; width: 34px; height: 34px; border-radius: 50%; font-weight: bold; cursor: pointer; font-size: 16px; color: #374151;"
        >
          ✕
        </button>

        <!-- Gambar Bersaiz Penuh -->
        <img 
          :src="previewImage.image_url" 
          :alt="previewImage.name" 
          style="max-width: 100%; max-height: 360px; object-fit: contain; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee;" 
        />

        <!-- Maklumat Produk -->
        <h3 style="margin: 0 0 6px 0; font-size: 18px; color: #111827;">{{ previewImage.name }}</h3>
        <p style="color: #2e7d32; font-size: 20px; font-weight: bold; margin: 0 0 10px 0;">RM {{ Number(previewImage.price).toFixed(2) }}</p>
        <p style="color: #4b5563; font-size: 14px; margin: 0; line-height: 1.5;">{{ previewImage.description }}</p>
      </div>
    </div>
  </div>
</template>