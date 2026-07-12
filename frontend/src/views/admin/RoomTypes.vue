<template>
  <div class="container">
    <div class="flex justify-between items-center mb-1">
      <h2>Kelola Tipe Kamar</h2>
      <button class="btn btn-primary btn-sm" @click="openAdd">Tambah Tipe Kamar</button>
    </div>

    <AppSkeleton v-if="loading" type="table-row" :count="4" />

    <div v-else-if="roomTypes.length === 0">
      <AppEmptyState type="default" message="Belum ada tipe kamar. Tambahkan sekarang!">
        <template #action>
          <button class="btn btn-primary btn-sm" @click="openAdd">Tambah Tipe Kamar</button>
        </template>
      </AppEmptyState>
    </div>

    <div v-else class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Harga/Malam</th>
            <th>Kapasitas</th>
            <th>Kamar Tersedia</th>
            <th>Gambar</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rt in roomTypes" :key="rt.id">
            <td>#{{ rt.id }}</td>
            <td>{{ rt.nama }}</td>
            <td>Rp {{ formatHarga(rt.harga_per_malam) }}</td>
            <td>{{ rt.kapasitas }} tamu</td>
            <td>{{ rt.available_rooms ?? '—' }}</td>
            <td><img v-if="rt.gambar" :src="apiBase + '/' + rt.gambar" class="gambar-thumb" /><span v-else class="text-muted">—</span></td>
            <td><span class="badge" :class="rt.is_active ? 'badge-active' : 'badge-inactive'">{{ rt.is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td>
              <button class="btn btn-ghost btn-sm" @click="openEdit(rt)">Edit</button>
              <button class="btn btn-danger btn-sm" @click="confirmDelete(rt.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AppModal :open="showModal" :title="editing ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar'" @close="closeModal">
      <div class="form-group">
        <label>Nama</label>
        <input v-model="form.nama" />
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <textarea v-model="form.deskripsi" rows="3"></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Harga/Malam (Rp)</label>
          <input v-model.number="form.harga_per_malam" type="number" min="0" />
        </div>
        <div class="form-group">
          <label>Kapasitas</label>
          <input v-model.number="form.kapasitas" type="number" min="1" />
        </div>
      </div>
      <div class="form-group">
        <label>Gambar</label>
        <div class="file-upload-wrapper mt-0-5">
          <input type="file" id="file-input" accept="image/*" class="hidden-input" @change="onFileChange" />
          <label for="file-input" class="custom-file-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Pilih Gambar Kamar...</span>
          </label>
        </div>
        <div v-if="gambarPreview" class="gambar-preview mt-1">
          <img :src="gambarPreview" class="gambar-preview-img" />
        </div>
        <p v-if="uploading" class="text-muted fs-0-9 mt-0-5">Mengupload...</p>
      </div>
      <div class="form-group">
        <label>Fasilitas (pisahkan dengan koma)</label>
        <textarea v-model="form.fasilitas" rows="2" placeholder="AC, TV, WiFi, Breakfast"></textarea>
      </div>
      <div v-if="modalError" class="alert alert-error">{{ modalError }}</div>
      <template #footer>
        <button class="btn btn-ghost" @click="closeModal">Batal</button>
        <button class="btn btn-primary" :disabled="saving || uploading" @click="handleSave">
          <AppSpinner v-if="saving" variant="ring" size="sm" /> {{ saving ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </template>
    </AppModal>

    <AppConfirm :open="confirmOpen" message="Yakin ingin menghapus tipe kamar ini?" action-label="Hapus" @confirm="doDelete" @cancel="confirmOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { getRooms, createRoomType, updateRoomType, deleteRoomType, uploadFile } from '../../api'

const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000'
const { proxy } = getCurrentInstance()

const roomTypes = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const saving = ref(false)
const modalError = ref('')
const confirmOpen = ref(false)
const deleteId = ref(null)
const gambarPreview = ref('')
const uploading = ref(false)
const form = ref({ nama: '', deskripsi: '', harga_per_malam: 0, kapasitas: 2, gambar: '', fasilitas: '' })

async function fetchRoomTypes() {
  loading.value = true
  try {
    const res = await getRooms()
    roomTypes.value = res.data
  } catch (e) {
    proxy.$toast(e.message || 'Gagal memuat data', 'error')
  } finally {
    loading.value = false
  }
}

function openAdd() {
  editing.value = null
  form.value = { nama: '', deskripsi: '', harga_per_malam: 0, kapasitas: 2, gambar: '', fasilitas: '' }
  gambarPreview.value = ''
  modalError.value = ''
  showModal.value = true
}

function openEdit(rt) {
  editing.value = rt.id
  form.value = { nama: rt.nama, deskripsi: rt.deskripsi || '', harga_per_malam: rt.harga_per_malam, kapasitas: rt.kapasitas, gambar: rt.gambar || '', fasilitas: rt.fasilitas || '' }
  gambarPreview.value = rt.gambar ? apiBase + '/' + rt.gambar : ''
  modalError.value = ''
  showModal.value = true
}

function closeModal() { showModal.value = false; editing.value = null; }

async function onFileChange(e) {
  const file = e.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => { gambarPreview.value = ev.target.result }
  reader.readAsDataURL(file)
  uploading.value = true
  try {
    const res = await uploadFile(file)
    form.value.gambar = res.data.foto
  } catch (err) {
    gambarPreview.value = ''
    proxy.$toast(err.message || 'Gagal upload gambar', 'error')
  } finally {
    uploading.value = false
  }
}

async function handleSave() {
  modalError.value = ''
  if (!form.value.nama.trim()) { modalError.value = 'Nama tidak boleh kosong'; return }
  if (!form.value.harga_per_malam || form.value.harga_per_malam <= 0) { modalError.value = 'Harga harus lebih dari 0'; return }
  saving.value = true
  try {
    if (editing.value) {
      await updateRoomType({ id: editing.value, ...form.value })
      proxy.$toast('Tipe kamar berhasil diupdate', 'success')
    } else {
      await createRoomType(form.value)
      proxy.$toast('Tipe kamar berhasil ditambahkan', 'success')
    }
    closeModal()
    await fetchRoomTypes()
  } catch (e) {
    modalError.value = e.message || 'Gagal menyimpan'
  } finally { saving.value = false; }
}

function confirmDelete(id) { deleteId.value = id; confirmOpen.value = true; }

async function doDelete() {
  confirmOpen.value = false
  try {
    await deleteRoomType(deleteId.value)
    proxy.$toast('Tipe kamar berhasil dihapus', 'success')
    await fetchRoomTypes()
  } catch (e) {
    proxy.$toast(e.message || 'Gagal menghapus', 'error')
  }
}

function formatHarga(h) { return new Intl.NumberFormat('id-ID').format(h); }

onMounted(fetchRoomTypes)
</script>

<style scoped>
.gambar-thumb {
  width: 48px;
  height: 36px;
  object-fit: cover;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border-subtle);
}
.gambar-preview-img {
  width: 160px;
  height: 120px;
  object-fit: cover;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border-subtle);
}
.hidden-input {
  display: none;
}
.custom-file-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  background: var(--bg-surface);
  border: 1px dashed var(--accent-primary);
  border-radius: var(--radius-sm);
  color: var(--accent-primary);
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: var(--text-sm);
}
.custom-file-btn:hover {
  background: var(--accent-primary-muted);
}
</style>
