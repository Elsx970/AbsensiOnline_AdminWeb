<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_lokasi'];
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];
    $radius = $_POST['radius'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $pertemuan = $_POST['pertemuan'];
    
    $stmt = $conn->prepare("INSERT INTO lokasi (nama_lokasi, pertemuan, latitude, longitude, radius, tanggal, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siddisss", $nama, $pertemuan, $lat, $lng, $radius, $tanggal, $jam_mulai, $jam_selesai);
    if($stmt->execute()) {
        $success = "Kelas/Lokasi berhasil ditambahkan!";
    } else {
        $error = "Gagal menyimpan data!";
    }
}
?>

<!-- Tambahkan Library Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Control Geocoder -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<!-- Flatpickr for 24h Time Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Tambah Mata Kuliah & Jadwal</h3>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Mata Kuliah</label>
                        <input type="text" name="nama_lokasi" class="form-control" required placeholder="Cth: Pemrograman Mobile">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pertemuan Ke-</label>
                        <input type="number" name="pertemuan" class="form-control" required placeholder="Cth: 1" value="1" min="1">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" id="lat" class="form-control" required placeholder="-5.364446">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="lng" class="form-control" required placeholder="105.243501">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kelas</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="text" name="jam_mulai" class="form-control timepicker" required placeholder="08:00">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="text" name="jam_selesai" class="form-control timepicker" required placeholder="10:00">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Radius Presensi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="radius" id="radiusInput" class="form-control" required value="50">
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn w-100 text-white" style="background-color: #003366;">
                        <i class="bi bi-save me-1"></i> Simpan Mata Kuliah
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <small class="text-primary fw-bold"><i class="bi bi-info-circle me-1"></i> Klik pada peta untuk menentukan titik koordinat kehadiran.</small>
            </div>
            <div class="card-body p-2">
                <div id="map" style="width: 100%; height: 450px; border-radius: 10px; z-index: 1;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var map = L.map('map').setView([-5.364446, 105.243501], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add Search Control (Geocoder)
    L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Cari lokasi..."
    })
    .on('markgeocode', function(e) {
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]);
        map.fitBounds(poly.getBounds());
        
        var radiusValue = parseFloat(document.getElementById('radiusInput').value) || 50;
        setLocation(e.geocode.center.lat, e.geocode.center.lng, radiusValue);
    })
    .addTo(map);

    // Initialize Flatpickr for 24h time format
    flatpickr(".timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    var marker;
    var circle;

    // Fungsi untuk update form dan peta
    function setLocation(lat, lng, rad) {
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);

        if (marker) { map.removeLayer(marker); }
        if (circle) { map.removeLayer(circle); }

        marker = L.marker([lat, lng]).addTo(map);
        circle = L.circle([lat, lng], {
            color: 'blue',
            fillColor: '#3085d6',
            fillOpacity: 0.2,
            radius: rad
        }).addTo(map);
    }

    // Klik di Peta
    map.on('click', function(e) {
        var radiusValue = parseFloat(document.getElementById('radiusInput').value) || 50;
        setLocation(e.latlng.lat, e.latlng.lng, radiusValue);
    });

    // Update lingkaran jika radius diketik manual
    document.getElementById('radiusInput').addEventListener('input', function() {
        if (marker) {
            var lat = parseFloat(document.getElementById('lat').value);
            var lng = parseFloat(document.getElementById('lng').value);
            var rad = parseFloat(this.value) || 50;
            setLocation(lat, lng, rad);
        }
    });

    // Update peta jika latitude diketik manual
    document.getElementById('lat').addEventListener('input', function() {
        var lat = parseFloat(this.value);
        var lng = parseFloat(document.getElementById('lng').value);
        var rad = parseFloat(document.getElementById('radiusInput').value) || 50;
        if (!isNaN(lat) && !isNaN(lng)) {
            setLocation(lat, lng, rad);
            map.setView([lat, lng], map.getZoom());
        }
    });

    // Update peta jika longitude diketik manual
    document.getElementById('lng').addEventListener('input', function() {
        var lat = parseFloat(document.getElementById('lat').value);
        var lng = parseFloat(this.value);
        var rad = parseFloat(document.getElementById('radiusInput').value) || 50;
        if (!isNaN(lat) && !isNaN(lng)) {
            setLocation(lat, lng, rad);
            map.setView([lat, lng], map.getZoom());
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
