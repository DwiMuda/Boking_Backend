<template>
  <div>
    <div class="flex justify-between items-center mb-1">
      <h2>Kelola Layanan</h2>
      <button class="btn btn-primary" @click="openAdd">Tambah Layanan</button>
    </div>

    <div v-if="loading" class="loading">Memuat data...</div>
    <div v-else-if="services.length === 0" class="empty-state">Belum ada layanan</div>
    <div v-else class="card overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Durasi (menit)</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in services" :key="s.id">
            <td>#{{ s.id }}</td>
            <td>{{ s.nama }}</td>
            <td>Rp {{ formatHarga(s.harga) }}</td>
            <td>{{ s.durasi_menit }}</td>
            <td><span class="badge" :class="s.is_active ? 'badge-confirmed' : 'badge-cancelled'">{{ s.is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td>
              <button class="btn btn-outline fs-0-85 p-0-4-0-8" @click="openEdit(s)">Edit</button>
              <button class="btn btn-danger fs-0-85 p-0-4-0-8 ml-0-3" @click="handleDelete(s.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h3>{{ editing ? 'Edit Layanan' : 'Tambah Layanan' }}</h3>
        <div class="form-group">
          <label>Nama</label>
          <input v-model="form.nama" />
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea v-model="form.deskripsi" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Harga (Rp)</label>
          <input v-model.number="form.harga" type="number" min="0" />
        </div>
        <div class="form-group">
          <label>Durasi (menit)</label>
          <input v-model.number="form.durasi_menit" type="number" min="30" step="30" />
        </div>
        <div v-if="modalError" class="alert alert-danger">{{ modalError }}</div>
        <div class="flex gap-0-5 justify-end mt-1">
          <button class="btn btn-outline" @click="closeModal">Batal</button>
          <button class="btn btn-primary" :disabled="saving" @click="handleSave">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getServices, createService, updateService, deleteService } from '../../api'

const services = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const saving = ref(false)
const modalError = ref('')
const form = ref({ nama: '', deskripsi: '', harga: 0, durasi_menit: 60 })

async function fetchServices() {
  loading.value = true
  try {
    const res = await getServices()
    services.value = res.data
  } catch (e) {
    alert(e.message || 'Gagal memuat layanan')
  } finally {
    loading.value = false
  }
}

function openAdd() {
  editing.value = null
  form.value = { nama: '', deskripsi: '', harga: 0, durasi_menit: 60 }
  modalError.value = ''
  showModal.value = true
}

function openEdit(s) {
  editing.value = s.id
  form.value = { nama: s.nama, deskripsi: s.deskripsi || '', harga: s.harga, durasi_menit: s.durasi_menit }
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
    } else {
      await createService(form.value)
    }
    closeModal()
    await fetchServices()
  } catch (e) {
    modalError.value = e.message || 'Gagal menyimpan layanan'
  } finally {
    saving.value = false
  }
}

async function handleDelete(id) {
  if (!confirm('Yakin ingin menghapus layanan ini?')) return
  try {
    await deleteService(id)
    await fetchServices()
  } catch (e) {
    alert(e.message || 'Gagal menghapus layanan')
  }
}

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}

onMounted(fetchServices)
</script>
