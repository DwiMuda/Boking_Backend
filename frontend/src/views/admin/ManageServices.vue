<template>
  <div class="container">
    <div class="flex items-center gap-3 mb-1">
      <button class="btn btn-ghost btn-sm" @click="$router.push('/admin')">
        <ArrowLeftIcon :size="18" />
        Kembali ke Dashboard
      </button>
      <div class="flex justify-between items-center flex-1">
        <h2 class="mb-0">Kelola Layanan</h2>
        <button class="btn btn-primary btn-sm" @click="openAdd">Tambah Layanan</button>
      </div>
    </div>

    <AppSkeleton v-if="loading" type="table-row" :count="4" />

    <div v-else-if="services.length === 0">
      <AppEmptyState type="default" message="Belum ada layanan. Tambahkan layanan pertama Anda!">
        <template #action>
          <button class="btn btn-primary btn-sm" @click="openAdd">Tambah Layanan</button>
        </template>
      </AppEmptyState>
    </div>

    <div v-else class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Durasi</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in services" :key="s.id">
            <td>#{{ s.id }}</td>
            <td>{{ s.nama }}</td>
            <td><span class="badge" :class="s.kategori === 'spa' ? 'badge-active' : 'badge-gold'">{{ s.kategori }}</span></td>
            <td>Rp {{ formatHarga(s.harga) }}</td>
            <td>{{ s.durasi_menit }} menit</td>
            <td><span class="badge" :class="s.is_active ? 'badge-active' : 'badge-inactive'">{{ s.is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td>
              <button class="btn btn-ghost btn-sm" @click="openEdit(s)">Edit</button>
              <button class="btn btn-danger btn-sm" @click="confirmDelete(s.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AppModal :open="showModal" :title="editing ? 'Edit Layanan' : 'Tambah Layanan'" @close="closeModal">
      <div class="form-group">
        <label>Nama</label>
        <input v-model="form.nama" />
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <textarea v-model="form.deskripsi" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label>Kategori</label>
        <select v-model="form.kategori">
          <option value="spa">Spa &amp; Wellness</option>
          <option value="dining">Restoran &amp; Dining</option>
          <option value="other">Lainnya</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Harga (Rp)</label>
          <input v-model.number="form.harga" type="number" min="0" />
        </div>
        <div class="form-group">
          <label>Durasi (menit)</label>
          <input v-model.number="form.durasi_menit" type="number" min="30" step="30" />
        </div>
      </div>
      <div class="form-group">
        <label>Fasilitas (opsional)</label>
        <textarea v-model="form.fasilitas" rows="2" placeholder="Misal: Handuk, Minuman, Ruang AC"></textarea>
      </div>
      <div v-if="modalError" class="alert alert-error">{{ modalError }}</div>
      <template #footer>
        <button class="btn btn-ghost" @click="closeModal">Batal</button>
        <button class="btn btn-primary" :disabled="saving" @click="handleSave">
          <AppSpinner v-if="saving" variant="ring" size="sm" />
          {{ saving ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </template>
    </AppModal>

    <AppConfirm :open="confirmOpen" message="Yakin ingin menghapus layanan ini?" action-label="Hapus" @confirm="doDelete" @cancel="confirmOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { getServices, createService, updateService, deleteService } from '../../api'
import { ArrowLeftIcon } from '@lucide/vue'

const { proxy } = getCurrentInstance()

const services = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const saving = ref(false)
const modalError = ref('')
const confirmOpen = ref(false)
const deleteId = ref(null)
const form = ref({ nama: '', deskripsi: '', harga: 0, durasi_menit: 60, kategori: 'spa', fasilitas: '' })

async function fetchServices() {
  loading.value = true
  try {
    const res = await getServices()
    services.value = res.data
  } catch (e) {
    proxy.$toast(e.message || 'Gagal memuat layanan', 'error')
  } finally {
    loading.value = false
  }
}

function openAdd() {
  editing.value = null
  form.value = { nama: '', deskripsi: '', harga: 0, durasi_menit: 60, kategori: 'spa', fasilitas: '' }
  modalError.value = ''
  showModal.value = true
}

function openEdit(s) {
  editing.value = s.id
  form.value = { nama: s.nama, deskripsi: s.deskripsi || '', harga: s.harga, durasi_menit: s.durasi_menit, kategori: s.kategori || 'spa', fasilitas: s.fasilitas || '' }
  modalError.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editing.value = null
}

async function handleSave() {
  modalError.value = ''
  if (!form.value.nama.trim()) { modalError.value = 'Nama tidak boleh kosong'; return }
  if (!form.value.harga || form.value.harga <= 0) { modalError.value = 'Harga harus lebih dari 0'; return }
  if (!form.value.durasi_menit || form.value.durasi_menit < 30) { modalError.value = 'Durasi minimal 30 menit'; return }
  saving.value = true
  try {
    if (editing.value) {
      await updateService({ id: editing.value, ...form.value })
      proxy.$toast('Layanan berhasil diupdate', 'success')
    } else {
      await createService(form.value)
      proxy.$toast('Layanan berhasil ditambahkan', 'success')
    }
    closeModal()
    await fetchServices()
  } catch (e) {
    modalError.value = e.message || 'Gagal menyimpan layanan'
  } finally {
    saving.value = false
  }
}

function confirmDelete(id) {
  deleteId.value = id
  confirmOpen.value = true
}

async function doDelete() {
  confirmOpen.value = false
  try {
    await deleteService(deleteId.value)
    proxy.$toast('Layanan berhasil dihapus', 'success')
    await fetchServices()
  } catch (e) {
    proxy.$toast(e.message || 'Gagal menghapus layanan', 'error')
  }
}

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}

onMounted(fetchServices)
</script>