<script setup>
const config = useRuntimeConfig()

// ============================================================================
// BAHAGIAN 1: PENGURUSAN IDENTITI, TOKEN & PROFIL (MILESTONE 2 - AUTH & RBAC)
// ============================================================================
const authToken = useCookie('auth_token', { maxAge: 60 * 60 * 24 * 7 }) // Simpan token 7 hari
const currentUser = ref(null)
const authMode = ref('login') // 'login' | 'register'
const showProfileModal = ref(false)
const authMessage = ref('')
const authError = ref('')
const authLoading = ref(false)

const authForm = ref({
  name: '',
  email: '',
  password: ''
})

const profileForm = ref({
  name: '',
  email: '',
  password: ''
})

// Semak adakah pengguna semasa ialah Admin
const isAdmin = computed(() => currentUser.value?.role === 'admin')

// Fungsi untuk menghasilkan Header Authorization bersama Bearer Token
const getAuthHeaders = () => {
  return authToken.value
    ? { Authorization: `Bearer ${authToken.value}`, Accept: 'application/json' }
    : { Accept: 'application/json' }
}

// Tarik maklumat pengguna semasa (GET /api/me) jika token wujud
const fetchCurrentUser = async () => {
  if (!authToken.value) {
    currentUser.value = null
    return
  }
  try {
    const res = await $fetch('/me', {
      baseURL: config.public.apiBase,
      headers: getAuthHeaders()
    })
    currentUser.value = res.data
    profileForm.value = {
      name: res.data.name,
      email: res.data.email,
      password: ''
    }
  } catch (err) {
    // Jika token tidak sah atau telah dipadam di database, kosongkan token
    authToken.value = null
    currentUser.value = null
  }
}

// Panggil fetchCurrentUser semasa halaman mula-mula dibuka
await fetchCurrentUser()

// Fungsi Log Masuk (POST /api/login) atau Daftar (POST /api/register)
const handleAuthSubmit = async () => {
  authLoading.value = true
  authMessage.value = ''
  authError.value = ''

  const endpoint = authMode.value === 'login' ? '/login' : '/register'

  try {
    const res = await $fetch(endpoint, {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: { Accept: 'application/json' },
      body: authForm.value
    })

    authToken.value = res.data.token
    currentUser.value = res.data.user
    profileForm.value = {
      name: res.data.user.name,
      email: res.data.user.email,
      password: ''
    }
    authMessage.value = `✅ ${res.message} (Selamat datang, ${res.data.user.name}!)`
    authForm.value = { name: '', email: '', password: '' }
  } catch (err) {
    authError.value = err?.data?.message || '❌ Gagal log masuk / mendaftar. Sila semak maklumat anda.'
  } finally {
    authLoading.value = false
  }
}

// Fungsi Kemas Kini Profil Sendiri (PUT /api/profile)
const handleUpdateProfile = async () => {
  authLoading.value = true
  authMessage.value = ''
  authError.value = ''

  try {
    const res = await $fetch('/profile', {
      baseURL: config.public.apiBase,
      method: 'PUT',
      headers: getAuthHeaders(),
      body: profileForm.value
    })
    currentUser.value = res.data
    profileForm.value.password = ''
    authMessage.value = '✅ Profil anda berjaya dikemaskini!'
    showProfileModal.value = false
  } catch (err) {
    authError.value = err?.data?.message || '❌ Gagal mengemaskini profil.'
  } finally {
    authLoading.value = false
  }
}

// Fungsi Log Keluar (POST /api/logout)
const handleLogout = async () => {
  try {
    await $fetch('/logout', {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: getAuthHeaders()
    })
  } catch (err) {
    // Abaikan jika token memang sudah luput
  } finally {
    authToken.value = null
    currentUser.value = null
    showProfileModal.value = false
    editingId.value = null
    authMessage.value = '👋 Anda telah berjaya log keluar.'
  }
}

// Fungsi bantuan isi pantas (Quick Fill) untuk tujuan ujian
const quickFill = (role) => {
  authMode.value = 'login'
  authError.value = ''
  if (role === 'admin') {
    authForm.value = { name: '', email: 'admin@coe.com', password: 'password123' }
  } else {
    authForm.value = { name: '', email: 'customer@coe.com', password: 'password123' }
  }
}

// ============================================================================
// BAHAGIAN 2: KATALOG PRODUK, CARIAN, SUSUNAN & CRUD
// ============================================================================
const search = ref('')
const sortBy = ref('created_at')
const order = ref('desc')
const page = ref(1)
const searchInput = ref('')

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
  baseURL: config.public.apiBase
})

const handleSearch = () => {
  search.value = searchInput.value
  page.value = 1
}

const products = computed(() => responseData.value?.data || [])
const meta = computed(() => responseData.value?.meta || { current_page: 1, last_page: 1, total: 0 })

// 2. Pemboleh ubah borang produk (Form State)
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
const editingId = ref(null)

// 3. Fungsi menghantar data borang ke Laravel (POST / PUT /api/products) bersama Token
async function submitProduct() {
  submitting.value = true
  formMessage.value = ''

  try {
    if (editingId.value) {
      await $fetch(`/products/${editingId.value}`, {
        baseURL: config.public.apiBase,
        method: 'PUT',
        headers: getAuthHeaders(),
        body: form.value
      })
      formMessage.value = '✅ Produk berjaya dikemaskini!'
      editingId.value = null
    } else {
      await $fetch('/products', {
        baseURL: config.public.apiBase,
        method: 'POST',
        headers: getAuthHeaders(),
        body: form.value
      })
      formMessage.value = '✅ Produk berjaya ditambah!'
    }

    form.value = { name: '', price: '', stock: 10, description: '', image_url: '', category_id: '' }
    refresh()
  } catch (err) {
    console.error(err)
    formMessage.value = `❌ ${err?.data?.message || 'Gagal menyimpan produk. Sila semak input atau kebenaran anda.'}`
  } finally {
    submitting.value = false
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Adakah anda pasti mahu memadam produk ini?')) {
    return
  }

  try {
    await $fetch(`/products/${id}`, {
      baseURL: config.public.apiBase,
      method: 'DELETE',
      headers: getAuthHeaders()
    })
    refresh()
  } catch (err) {
    alert(err?.data?.message || 'Gagal memadam produk.')
  }
}

const editProduct = (product) => {
  editingId.value = product.id
  form.value = {
    name: product.name,
    price: product.price,
    stock: product.stock,
    description: product.description || '',
    image_url: product.image_url || '',
    category_id: product.category_id || '',
  }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const cancelEdit = () => {
  editingId.value = null
  form.value = { name: '', price: '', stock: 10, description: '', image_url: '', category_id: '' }
  formMessage.value = ''
}
</script>

<template>
  <div style="padding: 30px; font-family: system-ui, sans-serif; max-width: 950px; margin: 0 auto;">
    <h1>🛍️ Katalog Produk COE (Milestone 2: Identity & State)</h1>
    <p style="color: #666; margin-bottom: 20px;">Sistem E-Dagang dengan Laravel Sanctum Token & Kawalan Peranan (RBAC)</p>

    <!-- ================================================================== -->
    <!-- PANEL PENGESAHAN PENGGUNA (LOGIN / REGISTER / PROFIL SEMASA)       -->
    <!-- ================================================================== -->
    <div style="background: #ffffff; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 2px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
      
      <!-- Mesej Status Auth -->
      <div v-if="authMessage" style="padding: 10px; margin-bottom: 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-weight: bold;">
        {{ authMessage }}
      </div>
      <div v-if="authError" style="padding: 10px; margin-bottom: 12px; background: #fee2e2; color: #991b1b; border-radius: 6px; font-weight: bold;">
        {{ authError }}
      </div>

      <!-- KEADAAN 1: PENGGUNA SUDAH LOG MASUK -->
      <div v-if="currentUser" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <span style="font-size: 16px;">👤 Log masuk sebagai: <strong>{{ currentUser.name }}</strong> ({{ currentUser.email }})</span>
          <span
            :style="{
              marginLeft: '10px',
              padding: '4px 10px',
              borderRadius: '999px',
              fontSize: '12px',
              fontWeight: 'bold',
              textTransform: 'uppercase',
              background: isAdmin ? '#f3e8ff' : '#dbeafe',
              color: isAdmin ? '#7e22ce' : '#1d4ed8'
            }"
          >
            {{ isAdmin ? '🛡️ ADMIN' : '🛒 CUSTOMER' }}
          </span>
        </div>

        <div style="display: flex; gap: 8px;">
          <button
            @click="showProfileModal = !showProfileModal"
            style="background: #0284c7; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: bold;"
          >
            ⚙️ {{ showProfileModal ? 'Tutup Profil' : 'Edit Profil' }}
          </button>
          <button
            @click="handleLogout"
            style="background: #dc2626; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: bold;"
          >
            🚪 Log Keluar
          </button>
        </div>

        <!-- Borang Kemas Kini Profil Sendiri -->
        <div v-if="showProfileModal" style="width: 100%; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
          <h3 style="margin-top: 0;">✏️ Kemas Kini Profil Saya</h3>
          <form @submit.prevent="handleUpdateProfile" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div>
              <label style="font-size: 13px; font-weight: bold;">Nama Baru *</label><br />
              <input v-model="profileForm.name" type="text" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
            </div>
            <div>
              <label style="font-size: 13px; font-weight: bold;">Emel *</label><br />
              <input v-model="profileForm.email" type="email" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
            </div>
            <div>
              <label style="font-size: 13px; font-weight: bold;">Password Baru (Pilihan)</label><br />
              <input v-model="profileForm.password" type="password" placeholder="Biarkan kosong jika kekal" style="width: 100%; padding: 8px; box-sizing: border-box;" />
            </div>
            <button type="submit" :disabled="authLoading" style="background: #16a34a; color: white; border: none; padding: 9px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">
              Simpan
            </button>
          </form>
        </div>
      </div>

      <!-- KEADAAN 2: PENGGUNA BELUM LOG MASUK (PAPAR BORANG LOGIN / REGISTER) -->
      <div v-else>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <div style="display: flex; gap: 8px;">
            <button
              type="button"
              @click="authMode = 'login'"
              :style="{
                padding: '6px 14px',
                borderRadius: '6px',
                border: 'none',
                cursor: 'pointer',
                fontWeight: 'bold',
                background: authMode === 'login' ? '#1e293b' : '#e2e8f0',
                color: authMode === 'login' ? 'white' : '#334155'
              }"
            >
              🔐 Log Masuk (Login)
            </button>
            <button
              type="button"
              @click="authMode = 'register'"
              :style="{
                padding: '6px 14px',
                borderRadius: '6px',
                border: 'none',
                cursor: 'pointer',
                fontWeight: 'bold',
                background: authMode === 'register' ? '#1e293b' : '#e2e8f0',
                color: authMode === 'register' ? 'white' : '#334155'
              }"
            >
              📝 Daftar Baru (Register)
            </button>
          </div>

          <!-- Butang Isi Pantas untuk Demo/Ujian -->
          <div style="font-size: 13px;">
            <span style="color: #64748b; margin-right: 6px;">Isi Pantas:</span>
            <button type="button" @click="quickFill('admin')" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-right: 6px;">
              🛡️ Akaun Admin
            </button>
            <button type="button" @click="quickFill('customer')" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
              🛒 Akaun Customer
            </button>
          </div>
        </div>

        <form @submit.prevent="handleAuthSubmit" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
          <div v-if="authMode === 'register'" style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: bold;">Nama Penuh *</label><br />
            <input v-model="authForm.name" type="text" required placeholder="Contoh: Uwais" style="width: 100%; padding: 8px; box-sizing: border-box;" />
          </div>
          <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: bold;">Alamat Emel *</label><br />
            <input v-model="authForm.email" type="email" required placeholder="admin@coe.com" style="width: 100%; padding: 8px; box-sizing: border-box;" />
          </div>
          <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: bold;">Kata Laluan *</label><br />
            <input v-model="authForm.password" type="password" required placeholder="******" style="width: 100%; padding: 8px; box-sizing: border-box;" />
          </div>
          <button type="submit" :disabled="authLoading" style="background: #2563eb; color: white; border: none; padding: 9px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            {{ authLoading ? 'Memproses...' : (authMode === 'login' ? 'Masuk' : 'Daftar') }}
          </button>
        </form>
      </div>
    </div>

    <!-- ================================================================== -->
    <!-- BORANG TAMBAH / EDIT PRODUK (HANYA MUNCUL UNTUK ADMIN SAHAJA!)     -->
    <!-- ================================================================== -->
    <div v-if="isAdmin" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #ddd;">
      <h2>{{ editingId ? '✏️ Kemaskini Produk (Mod Admin)' : '➕ Tambah Produk Baharu (Mod Admin)' }}</h2>

      <div v-if="formMessage" style="padding: 10px; margin-bottom: 15px; background: #e3f2fd; border-radius: 4px; font-weight: bold;">
        {{ formMessage }}
      </div>

      <form @submit.prevent="submitProduct" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
          <label>Nama Produk *</label><br />
          <input v-model="form.name" type="text" required style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" placeholder="Contoh: Monitor Gaming" />
        </div>

        <div>
          <label>Harga (RM) *</label><br />
          <input v-model="form.price" type="number" step="0.01" min="0" required style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" placeholder="0.00" />
        </div>

        <div>
          <label>Kategori</label><br />
          <select
            v-model="form.category_id"
            style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;"
            :style="{ color: !form.category_id ? '#757575' : '#000' }"
          >
            <option value="" disabled selected>Pilih Kategori...</option>
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
          <input v-model="form.stock" type="number" min="0" required style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" placeholder="0" />
        </div>

        <div style="grid-column: span 2;">
          <label>Pautan Gambar (URL)</label><br />
          <input v-model="form.image_url" type="url" style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" placeholder="https://..." />
        </div>

        <div style="grid-column: span 2;">
          <label>Penerangan Produk *</label><br />
          <textarea v-model="form.description" required rows="2" style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" placeholder="Penerangan ringkas..."></textarea>
        </div>

        <div style="grid-column: span 2;">
          <button type="submit" :disabled="submitting" style="background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer;">
            {{ submitting ? 'Menyimpan...' : '➕ Simpan Produk' }}
          </button>
          <button v-if="editingId" type="button" @click="cancelEdit" style="margin-left: 10px; background-color: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
            Batal
          </button>
        </div>
      </form>
    </div>

    <!-- Makluman jika bukan Admin -->
    <div v-else style="background: #f1f5f9; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; color: #475569; font-size: 14px; border-left: 4px solid #64748b;">
      ℹ️ <strong>Mod Baca Sahaja (Read-Only):</strong> Hanya pengguna dengan peranan <strong>Admin</strong> dibenarkan menambah, mengemaskini, atau memadam produk.
    </div>

    <!-- ================================================================== -->
    <!-- JADUAL PAPARAN PRODUK                                              -->
    <!-- ================================================================== -->
    <div v-if="pending">⏳ Memuatkan data produk dari Laravel API...</div>
    <div v-else-if="error" style="color: red; padding: 15px; border: 1px solid red; border-radius: 8px;">
      ⚠️ Gagal menyambung ke Laravel API. Pastikan container backend sedang berjalan di port 8000!
    </div>

    <div v-else>
      <!-- Bar Carian & Susunan -->
      <div style="display: flex; gap: 12px; margin-bottom: 16px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
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
            <th v-if="isAdmin">Tindakan Admin</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>#{{ product.id }}</td>
            <td>
              <div><strong>{{ product.name }}</strong></div>
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
            <td v-if="isAdmin">
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