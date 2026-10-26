import { mkdir, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const outputDirectory = dirname(fileURLToPath(import.meta.url));
const screens = [
    { role: 'PUBLIC', title: 'Login', section: 'Autentikasi', icon: 'CT', kind: 'login' },
    { role: 'ADMIN', title: 'Dashboard Admin', section: 'Dashboard', icon: 'DB', kind: 'dashboard-admin' },
    { role: 'ADMIN', title: 'Denah Fasilitas', section: 'Operasional', icon: 'MP', kind: 'floorplan-admin' },
    { role: 'ADMIN', title: 'Galeri & 360°', section: 'Operasional', icon: '36', kind: 'gallery-admin' },
    { role: 'ADMIN', title: 'Pemeriksaan Baru', section: 'Operasional', icon: 'CK', kind: 'inspection-admin' },
    { role: 'ADMIN', title: 'Data Area Ruangan', section: 'Master Data', icon: 'AR', kind: 'areas' },
    { role: 'ADMIN', title: 'Data Pengguna', section: 'Master Data', icon: 'US', kind: 'users' },
    { role: 'ADMIN', title: 'Riwayat Monitoring', section: 'Monitoring & Laporan', icon: 'RH', kind: 'history-admin' },
    { role: 'ADMIN', title: 'Laporan Kebersihan', section: 'Monitoring & Laporan', icon: 'LP', kind: 'reports-admin' },
    { role: 'ADMIN', title: 'Profil Saya', section: 'Pengaturan Akun', icon: 'PF', kind: 'profile-admin' },
    { role: 'PETUGAS', title: 'Dashboard Petugas', section: 'Dashboard', icon: 'DB', kind: 'dashboard-staff' },
    { role: 'PETUGAS', title: 'Denah Fasilitas', section: 'Operasional', icon: 'MP', kind: 'floorplan-staff' },
    { role: 'PETUGAS', title: 'Galeri & 360°', section: 'Operasional', icon: '36', kind: 'gallery-staff' },
    { role: 'PETUGAS', title: 'Scan QR Ruangan', section: 'Operasional', icon: 'QR', kind: 'scan' },
    { role: 'PETUGAS', title: 'Pemeriksaan Kebersihan', section: 'Operasional', icon: 'CK', kind: 'inspection-staff' },
    { role: 'PETUGAS', title: 'Riwayat Saya', section: 'Aktivitas Saya', icon: 'RH', kind: 'history-staff' },
    { role: 'PETUGAS', title: 'Laporan Kebersihan', section: 'Aktivitas Saya', icon: 'LP', kind: 'reports-staff' },
    { role: 'PETUGAS', title: 'Profil Saya', section: 'Pengaturan Akun', icon: 'PF', kind: 'profile-staff' },
];

const colors = {
    red: '#dc143c',
    redDark: '#b1002c',
    redSoft: '#fff1f0',
    ink: '#1b1c1c',
    muted: '#706b6a',
    line: '#e8e5e4',
    canvas: '#f6f5f4',
    white: '#ffffff',
    green: '#168447',
    greenSoft: '#e8f6ed',
    amber: '#b76b13',
    amberSoft: '#fff4df',
    blue: '#3478b8',
    blueSoft: '#eaf3fb',
};

const esc = (value) => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');

function rect(x, y, width, height, fill = colors.white, radius = 12, stroke = 'none') {
    return `<rect x="${x}" y="${y}" width="${width}" height="${height}" rx="${radius}" fill="${fill}"${stroke === 'none' ? '' : ` stroke="${stroke}"`}/>`;
}

function text(x, y, value, size = 13, fill = colors.ink, weight = 400, extra = '') {
    return `<text x="${x}" y="${y}" font-family="Inter,Arial,sans-serif" font-size="${size}" font-weight="${weight}" fill="${fill}"${extra ? ` ${extra}` : ''}>${esc(value)}</text>`;
}

function line(x1, y1, x2, y2, color = colors.line, width = 1) {
    return `<path d="M${x1} ${y1}H${x2}" stroke="${color}" stroke-width="${width}"/>`;
}

function badge(x, y, label, tone = 'green') {
    const fill = tone === 'red' ? colors.redSoft : tone === 'amber' ? colors.amberSoft : tone === 'blue' ? colors.blueSoft : colors.greenSoft;
    const ink = tone === 'red' ? colors.redDark : tone === 'amber' ? colors.amber : tone === 'blue' ? colors.blue : colors.green;
    const width = Math.max(60, label.length * 6.3 + 18);
    return `${rect(x, y - 15, width, 23, fill, 11)}${text(x + 9, y, label, 10, ink, 600)}`;
}

function button(x, y, label, primary = false, width = null) {
    const w = width ?? Math.max(100, label.length * 7 + 32);
    return `${rect(x, y, w, 38, primary ? colors.red : colors.white, 9, primary ? 'none' : colors.line)}${text(x + 15, y + 24, label, 11, primary ? colors.white : colors.ink, 600)}`;
}

function cardTitle(x, y, title, subtitle = '') {
    return `${text(x, y, title, 14, colors.ink, 700)}${subtitle ? text(x, y + 20, subtitle, 10, colors.muted, 400) : ''}`;
}

function shell(screen, active = screen.title) {
    const adminLinks = [
        ['Dashboard', 'DB'], ['Denah Fasilitas', 'MP'], ['Galeri & 360°', '36'], ['Pemeriksaan Baru', 'CK'],
        ['Data Area Ruangan', 'AR'], ['Data Pengguna', 'US'], ['Riwayat Monitoring', 'RH'], ['Laporan Kebersihan', 'LP'],
    ];
    const staffLinks = [
        ['Dashboard', 'DB'], ['Denah Fasilitas', 'MP'], ['Galeri & 360°', '36'], ['Scan QR Ruangan', 'QR'],
        ['Pemeriksaan Kebersihan', 'CK'], ['Riwayat Saya', 'RH'], ['Laporan Kebersihan', 'LP'],
    ];
    const links = screen.role === 'ADMIN' ? adminLinks : staffLinks;
    let sidebar = `${rect(0, 0, 240, 768, colors.white, 0)}${line(239, 0, 239, 768)}${rect(22, 20, 38, 38, colors.red, 11)}${text(31, 45, 'CT', 14, colors.white, 700)}${text(70, 37, 'CLEAN', 14, colors.ink, 800)}${text(112, 37, 'TRACK', 14, colors.red, 800)}${text(70, 52, 'MONITORING KEBERSIHAN', 7.5, colors.muted, 700)}`;
    sidebar += text(24, 91, screen.role === 'ADMIN' ? 'ADMINISTRATOR' : 'PETUGAS OPERASIONAL', 8, colors.muted, 700, 'letter-spacing="1"');
    let y = 111;
    for (const [label, icon] of links) {
        const isActive = active === label || (active === screen.title && label === screen.title);
        if (isActive) {
            sidebar += rect(14, y - 20, 211, 36, colors.redSoft, 9);
        }
        sidebar += `${rect(25, y - 12, 21, 21, isActive ? '#ffe0e4' : colors.canvas, 6)}${text(30, y + 3, icon, 8, isActive ? colors.red : colors.muted, 700)}${text(55, y + 3, label, 10, isActive ? colors.redDark : '#514c4b', isActive ? 700 : 500)}`;
        y += 42;
    }
    sidebar += `${line(20, 699, 220, 699)}${rect(24, 714, 34, 34, screen.role === 'ADMIN' ? '#2b5d4b' : '#805c92', 17)}${text(31, 735, screen.role === 'ADMIN' ? 'AD' : 'PT', 10, colors.white, 700)}${text(68, 727, screen.role === 'ADMIN' ? 'Admin CleanTrack' : 'Petugas Kebersihan', 9, colors.ink, 600)}${text(68, 742, screen.role === 'ADMIN' ? 'Administrator' : 'Petugas', 8, colors.muted, 400)}`;
    const toolbar = `${rect(240, 0, 1126, 66, colors.white, 0)}${line(240, 65, 1366, 65)}${text(270, 28, screen.title, 15, colors.ink, 700)}${text(270, 47, `CleanTrack  /  ${screen.section}`, 9, colors.muted, 400)}${rect(1195, 17, 34, 34, colors.canvas, 10)}${text(1207, 39, '⌕', 19, colors.muted, 500)}${rect(1242, 17, 34, 34, colors.canvas, 10)}${text(1254, 39, '♧', 15, colors.muted, 500)}${rect(1287, 15, 35, 35, '#f4d9dc', 18)}${text(1297, 38, 'AD', 9, colors.redDark, 700)}${text(1328, 37, '⌄', 12, colors.muted, 600)}`;
    return `${rect(0, 0, 1366, 768, colors.canvas, 0)}${sidebar}${toolbar}`;
}

function kpi(x, y, label, value, hint, tone = 'red', width = 202) {
    const color = tone === 'green' ? colors.green : tone === 'amber' ? colors.amber : tone === 'blue' ? colors.blue : colors.red;
    const tint = tone === 'green' ? colors.greenSoft : tone === 'amber' ? colors.amberSoft : tone === 'blue' ? colors.blueSoft : colors.redSoft;
    return `${rect(x, y, width, 94, colors.white, 12, colors.line)}${rect(x + 14, y + 14, 31, 31, tint, 9)}${text(x + 24, y + 35, tone === 'green' ? '✓' : tone === 'amber' ? '!' : tone === 'blue' ? '↗' : '⌂', 15, color, 700)}${text(x + 55, y + 27, label, 9, colors.muted, 600)}${text(x + 15, y + 68, value, 23, colors.ink, 700)}${text(x + 67, y + 67, hint, 8, colors.muted, 400)}`;
}

function table(x, y, width, headers, rows, options = {}) {
    const rowHeight = options.rowHeight ?? 38;
    const columns = options.columns ?? headers.map(() => width / headers.length);
    let result = rect(x, y, width, 36, '#f8f7f6', 7);
    let offset = 12;
    headers.forEach((header, index) => {
        result += text(x + offset, y + 22, header.toUpperCase(), 8, colors.muted, 700);
        offset += columns[index];
    });
    rows.forEach((row, rowIndex) => {
        const rowY = y + 36 + rowIndex * rowHeight;
        result += line(x, rowY + rowHeight, x + width, rowY + rowHeight);
        let cellX = x + 12;
        row.forEach((cell, cellIndex) => {
            if (typeof cell === 'object' && cell.badge) {
                result += badge(cellX, rowY + 23, cell.badge, cell.tone);
            } else {
                result += text(cellX, rowY + 23, cell, 9, cellIndex === 0 ? colors.ink : colors.muted, cellIndex === 0 ? 600 : 400);
            }
            cellX += columns[cellIndex];
        });
    });
    return result;
}

function chart(x, y, width, height, variant = 0) {
    const values = variant % 2 === 0 ? [48, 75, 57, 87, 64, 94, 70, 82] : [38, 64, 50, 78, 59, 88, 65, 75];
    const labels = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
    let result = '';
    for (let index = 0; index < 4; index += 1) {
        result += line(x, y + index * (height / 3), x + width, y + index * (height / 3), '#efedec');
    }
    const barWidth = width / 12;
    values.forEach((value, index) => {
        const barHeight = (height - 20) * value / 100;
        result += rect(x + 14 + index * (barWidth + 8), y + height - barHeight, barWidth, barHeight, index === 5 ? colors.red : '#f3a7b4', 5);
        if (index < labels.length) {
            result += text(x + 12 + index * (barWidth + 8), y + height + 18, labels[index], 8, colors.muted, 500);
        }
    });
    return result;
}

function floorplan(x, y, width, height, staff = false) {
    const rooms = [
        { x: 0.02, y: 0.08, w: 0.31, h: 0.38, name: 'Ruang Kelas A', state: 'Bersih', tone: 'green', fill: '#dff1e6' },
        { x: 0.37, y: 0.08, w: 0.28, h: 0.38, name: 'Laboratorium', state: 'Perlu cek', tone: 'amber', fill: '#fff0ce' },
        { x: 0.69, y: 0.08, w: 0.29, h: 0.38, name: 'Aula', state: 'Perlu dibersihkan', tone: 'red', fill: '#ffe2e5' },
        { x: 0.02, y: 0.53, w: 0.43, h: 0.39, name: 'Perpustakaan', state: 'Bersih', tone: 'green', fill: '#e6f2ec' },
        { x: 0.49, y: 0.53, w: 0.48, h: 0.39, name: 'Toilet Lantai 1', state: 'Sedang dibersihkan', tone: 'blue', fill: '#e4eef8' },
    ];
    let result = rect(x, y, width, height, '#f8f8f7', 10, colors.line);
    for (const room of rooms) {
        const rx = x + room.x * width;
        const ry = y + room.y * height;
        const rw = room.w * width;
        const rh = room.h * height;
        result += `${rect(rx, ry, rw, rh, room.fill, 9, '#d8d5d3')}${text(rx + 11, ry + 25, room.name, 10, colors.ink, 700)}${badge(rx + 10, ry + 50, room.state, room.tone)}`;
    }
    result += text(x + 10, y + height + 17, staff ? 'Lantai 1  ·  5 area  ·  Status diperbarui real-time' : 'Lantai 1  ·  Klik area untuk melihat detail dan riwayat', 8, colors.muted, 400);
    return result;
}

function galleryTiles(x, y, width, staff = false) {
    const items = [
        ['RUANG KELAS A', 'Terakhir diperbarui hari ini', '#d8ebe1'],
        ['LABORATORIUM', 'Foto kondisi terbaru', '#e5eaf0'],
        ['AULA UTAMA', 'Tampilan 360° tersedia', '#f3e5cf'],
    ];
    let result = '';
    const gap = 14;
    const tileWidth = (width - gap * 2) / 3;
    items.forEach((item, index) => {
        const tx = x + index * (tileWidth + gap);
        result += `${rect(tx, y, tileWidth, 185, colors.white, 11, colors.line)}${rect(tx + 1, y + 1, tileWidth - 2, 109, item[2], 10)}${rect(tx + tileWidth - 56, y + 12, 44, 23, colors.white, 12)}${text(tx + tileWidth - 46, y + 28, '360°', 9, colors.red, 700)}${text(tx + 13, y + 130, item[0], 10, colors.ink, 700)}${text(tx + 13, y + 149, item[1], 8, colors.muted, 400)}${text(tx + 13, y + 172, staff ? 'Lihat dokumentasi  →' : 'Kelola dokumentasi  →', 8, colors.red, 600)}`;
    });
    return result;
}

function field(x, y, width, label, value, multiline = false) {
    const height = multiline ? 74 : 38;
    return `${text(x, y, label, 9, colors.ink, 600)}${rect(x, y + 9, width, height, colors.white, 8, colors.line)}${text(x + 12, y + (multiline ? 33 : 33), value, 9, value.startsWith('Pilih') ? '#aaa4a2' : colors.muted, 400)}`;
}

function content(screen) {
    const x = 270;
    const contentWidth = 1054;
    let body = `${text(x, 94, screen.title, 20, colors.ink, 700)}${text(x, 115, 'Pantau dan kelola kebersihan fasilitas dalam satu tempat.', 10, colors.muted, 400)}`;
    const toolbar = (label = 'Hari ini, 22 Oktober 2026') => `${rect(1010, 83, 205, 34, colors.white, 8, colors.line)}${text(1024, 105, label, 9, colors.muted, 500)}${button(1227, 82, '+ Tambah', true, 97)}`;

    switch (screen.kind) {
        case 'dashboard-admin':
            body += kpi(x, 138, 'TOTAL AREA', '24', '+2 bulan ini', 'red', 198);
            body += kpi(x + 211, 138, 'SUDAH BERSIH', '18', '75% area', 'green', 198);
            body += kpi(x + 422, 138, 'PERLU TINDAKAN', '4', 'Perlu ditangani', 'amber', 198);
            body += kpi(x + 633, 138, 'PETUGAS AKTIF', '8', '2 sedang bertugas', 'blue', 198);
            body += kpi(x + 844, 138, 'INSPEKSI HARI INI', '32', '+12% kemarin', 'green', 210);
            body += rect(x, 249, 644, 244, colors.white, 12, colors.line) + cardTitle(x + 18, 278, 'Aktivitas pemeriksaan', 'Ringkasan inspeksi selama 7 hari terakhir') + chart(x + 27, 321, 575, 131, 0);
            body += rect(x + 660, 249, 394, 244, colors.white, 12, colors.line) + cardTitle(x + 678, 278, 'Kondisi area', 'Distribusi kondisi fasilitas') + badge(x + 682, 329, 'Bersih 18', 'green') + badge(x + 682, 366, 'Perlu dicek 2', 'amber') + badge(x + 682, 403, 'Perlu dibersihkan 4', 'red') + rect(x + 869, 322, 136, 136, 'none', 68, '#e4e1e0') + `<path d="M1001 344 A68 68 0 1 0 1002 434" fill="none" stroke="${colors.green}" stroke-width="18" stroke-linecap="round"/><path d="M1002 434 A68 68 0 0 0 989 337" fill="none" stroke="${colors.red}" stroke-width="18" stroke-linecap="round"/>` + text(x + 916, 390, '75%', 23, colors.ink, 700);
            body += rect(x, 509, contentWidth, 224, colors.white, 12, colors.line) + cardTitle(x + 18, 537, 'Monitoring terbaru', 'Aktivitas operasional terakhir') + table(x + 16, 553, 1022, ['Area', 'Petugas', 'Kondisi', 'Status', 'Waktu'], [['Ruang Kelas A', 'Nina Safitri', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '08:42'], ['Laboratorium', 'Dedi Kurnia', { badge: 'Kotor', tone: 'amber' }, { badge: 'Perlu tindakan', tone: 'amber' }, '08:15'], ['Toilet Lantai 1', 'Rina Putri', { badge: 'Sangat kotor', tone: 'red' }, { badge: 'Dibersihkan', tone: 'blue' }, '07:55']], { columns: [220, 185, 190, 245, 150], rowHeight: 34 });
            break;
        case 'dashboard-staff':
            body += `${text(x, 142, 'Selamat pagi, Nina 👋', 22, colors.ink, 700)}${text(x, 164, 'Berikut ringkasan area tugasmu untuk hari ini.', 10, colors.muted, 400)}${button(1082, 135, 'Mulai pemeriksaan', true, 191)}`;
            body += kpi(x, 190, 'AREA TUGAS', '12', 'Area aktif', 'red', 248) + kpi(x + 263, 190, 'SUDAH DIPERIKSA', '8', 'Hari ini', 'green', 248) + kpi(x + 526, 190, 'BELUM DIPERIKSA', '4', 'Selesaikan bertahap', 'blue', 248) + kpi(x + 789, 190, 'PERLU TINDAKAN', '2', 'Prioritas', 'amber', 248);
            body += rect(x, 303, 674, 430, colors.white, 12, colors.line) + cardTitle(x + 18, 331, 'Daftar area tugas', 'Status pemeriksaan area hari ini') + table(x + 16, 347, 642, ['Area', 'Lantai', 'Status', 'Aksi'], [['Ruang Kelas A', 'Lt. 1', { badge: 'Bersih', tone: 'green' }, 'Lihat  →'], ['Laboratorium', 'Lt. 1', { badge: 'Belum cek', tone: 'amber' }, 'Periksa  →'], ['Toilet Lantai 1', 'Lt. 1', { badge: 'Perlu tindakan', tone: 'red' }, 'Periksa  →'], ['Aula Utama', 'Lt. 2', { badge: 'Belum cek', tone: 'amber' }, 'Periksa  →'], ['Perpustakaan', 'Lt. 2', { badge: 'Selesai', tone: 'green' }, 'Lihat  →'], ['Ruang Guru', 'Lt. 2', { badge: 'Selesai', tone: 'green' }, 'Lihat  →'], ['Kantin', 'Lt. 1', { badge: 'Belum cek', tone: 'amber' }, 'Periksa  →']], { columns: [220, 85, 198, 120], rowHeight: 43 });
            body += rect(x + 694, 303, 360, 430, colors.white, 12, colors.line) + cardTitle(x + 712, 331, 'Akses cepat', 'Pemeriksaan lebih mudah') + rect(x + 712, 358, 324, 108, colors.redSoft, 10) + text(x + 732, 389, 'Scan QR ruangan', 14, colors.redDark, 700) + text(x + 732, 411, 'Pindai kode di area untuk mulai inspeksi', 9, colors.muted) + button(x + 732, 422, 'Buka scanner', true, 129) + rect(x + 712, 483, 324, 229, '#f8f7f6', 10) + cardTitle(x + 730, 512, 'Target hari ini', '8 dari 12 area sudah diperiksa') + rect(x + 730, 540, 286, 9, '#e6e2e1', 5) + rect(x + 730, 540, 191, 9, colors.green, 5) + badge(x + 730, 583, '4 area menunggu', 'amber') + text(x + 730, 633, 'Ingat lampirkan foto sebelum dan sesudah.', 9, colors.muted);
            break;
        case 'floorplan-admin':
        case 'floorplan-staff':
            body += toolbar('Lantai 1  ⌄');
            body += rect(x, 138, 701, 576, colors.white, 12, colors.line) + cardTitle(x + 18, 166, 'Denah ruangan', 'Pilih area untuk melihat status kebersihan') + floorplan(x + 18, 187, 665, 451, screen.kind.endsWith('staff'));
            body += rect(x + 720, 138, 334, 576, colors.white, 12, colors.line) + cardTitle(x + 740, 166, 'Detail area', 'Laboratorium · Lantai 1') + badge(x + 740, 205, 'Perlu pemeriksaan', 'amber') + text(x + 740, 246, 'Lokasi', 9, colors.muted, 600) + text(x + 740, 264, 'Gedung A, sisi timur', 11, colors.ink, 600) + line(x + 740, 283, x + 1034, 283) + text(x + 740, 309, 'Pemeriksaan terakhir', 9, colors.muted, 600) + text(x + 740, 329, 'Hari ini, 08:15 WIB', 11, colors.ink, 600) + text(x + 740, 358, 'Petugas', 9, colors.muted, 600) + text(x + 740, 378, 'Dedi Kurnia', 11, colors.ink, 600) + rect(x + 740, 401, 294, 100, '#eff2f2', 9) + text(x + 761, 455, 'Pratinjau ruangan', 10, colors.muted, 500) + button(x + 740, 520, 'Lihat galeri', false, 130) + button(x + 740, 568, 'Mulai pemeriksaan', true, 190);
            break;
        case 'gallery-admin':
        case 'gallery-staff':
            body += toolbar('Semua lantai  ⌄');
            body += `${rect(x, 138, contentWidth, 95, '#f8eded', 12)}${text(x + 22, 170, 'Dokumentasi visual fasilitas', 18, colors.ink, 700)}${text(x + 22, 194, 'Jelajahi kondisi ruangan melalui foto dan tampilan 360°.', 10, colors.muted)}${button(x + 840, 165, screen.role === 'ADMIN' ? '+ Unggah dokumentasi' : 'Buka denah', true, 190)}`;
            body += galleryTiles(x, 255, contentWidth, screen.role !== 'ADMIN');
            body += rect(x, 466, contentWidth, 245, colors.white, 12, colors.line) + cardTitle(x + 18, 495, 'Dokumentasi terbaru', 'Foto pemeriksaan terbaru dari petugas') + table(x + 16, 512, 1022, ['Area', 'Dokumentasi', 'Diunggah oleh', 'Tanggal', 'Aksi'], [['Ruang Kelas A', 'Sebelum & sesudah', 'Nina Safitri', '22 Okt 2026', 'Lihat'], ['Laboratorium', 'Foto 360°', 'Dedi Kurnia', '22 Okt 2026', 'Lihat'], ['Aula Utama', 'Foto kondisi', 'Rina Putri', '21 Okt 2026', 'Lihat']], { columns: [210, 250, 215, 190, 100], rowHeight: 36 });
            break;
        case 'inspection-admin':
        case 'inspection-staff':
            body += `${text(x, 143, 'Catat kondisi area dan dokumentasikan hasil pemeriksaan.', 10, colors.muted)}${badge(x + 901, 142, 'Langkah 1 dari 1', 'blue')}`;
            body += rect(x, 158, 681, 560, colors.white, 12, colors.line) + cardTitle(x + 20, 188, 'Informasi pemeriksaan', 'Lengkapi detail pemeriksaan ruangan') + field(x + 20, 216, 302, 'AREA YANG DIPERIKSA', 'Pilih area ruangan  ⌄') + field(x + 342, 216, 302, 'STATUS KEBERSIHAN', 'Pilih status  ⌄') + text(x + 20, 304, 'KONDISI KEBERSIHAN', 9, colors.ink, 600) + badge(x + 20, 334, 'Bersih', 'green') + badge(x + 103, 334, 'Cukup bersih', 'blue') + badge(x + 218, 334, 'Kotor', 'amber') + badge(x + 291, 334, 'Sangat kotor', 'red') + field(x + 20, 355, 624, 'CATATAN PEMERIKSAAN', 'Tuliskan kondisi dan tindakan yang diperlukan...', true) + text(x + 20, 466, 'DOKUMENTASI FOTO', 9, colors.ink, 600) + rect(x + 20, 480, 302, 104, '#faf9f8', 9, '#d9d4d2') + text(x + 99, 526, '＋  Unggah foto sebelum', 10, colors.muted, 500) + text(x + 79, 548, 'JPG / PNG · Maks. 5 MB', 8, colors.muted) + rect(x + 342, 480, 302, 104, '#faf9f8', 9, '#d9d4d2') + text(x + 426, 526, '＋  Unggah foto sesudah', 10, colors.muted, 500) + text(x + 401, 548, 'JPG / PNG · Maks. 5 MB', 8, colors.muted) + text(x + 20, 616, 'Dokumentasi 360° (opsional)', 9, colors.ink, 600) + rect(x + 20, 628, 624, 39, '#faf9f8', 8, '#d9d4d2') + text(x + 34, 652, '＋  Unggah foto panorama 360°', 9, colors.muted, 500) + button(x + 20, 678, 'Simpan pemeriksaan', true, 171);
            body += rect(x + 702, 158, 352, 300, colors.white, 12, colors.line) + cardTitle(x + 724, 188, 'Ringkasan area', 'Informasi ruangan terpilih') + rect(x + 724, 209, 308, 122, '#e7ebe8', 9) + text(x + 807, 276, 'Pratinjau foto ruangan', 9, colors.muted, 500) + text(x + 724, 363, 'Lantai', 9, colors.muted) + text(x + 1002, 363, '1', 9, colors.ink, 600) + line(x + 724, 375, x + 1032, 375) + text(x + 724, 400, 'Lokasi', 9, colors.muted) + text(x + 921, 400, 'Gedung A', 9, colors.ink, 600) + text(x + 724, 435, 'Pemeriksaan terakhir', 9, colors.muted) + text(x + 917, 435, 'Belum tersedia', 9, colors.ink, 600);
            body += rect(x + 702, 475, 352, 243, colors.redSoft, 12) + text(x + 724, 511, 'PANDUAN PEMERIKSAAN', 9, colors.redDark, 700, 'letter-spacing="0.7"') + text(x + 724, 543, '1. Pastikan area sudah benar.', 9, colors.ink) + text(x + 724, 568, '2. Catat kondisi secara objektif.', 9, colors.ink) + text(x + 724, 593, '3. Ambil foto yang jelas.', 9, colors.ink) + text(x + 724, 618, '4. Simpan sebelum meninggalkan area.', 9, colors.ink) + badge(x + 724, 664, 'Periksa kembali sebelum simpan', 'red');
            break;
        case 'areas':
            body += toolbar();
            body += kpi(x, 138, 'TOTAL AREA', '24', 'Terdaftar', 'red', 248) + kpi(x + 263, 138, 'AREA AKTIF', '22', 'Siap dimonitor', 'green', 248) + kpi(x + 526, 138, 'LANTAI', '4', 'Gedung A & B', 'blue', 248) + kpi(x + 789, 138, 'TANPA FOTO 360°', '3', 'Perlu dilengkapi', 'amber', 248);
            body += rect(x, 249, contentWidth, 474, colors.white, 12, colors.line) + text(x + 18, 280, 'Daftar area ruangan', 14, colors.ink, 700) + text(x + 18, 299, 'Kelola seluruh ruang yang dipantau CleanTrack.', 9, colors.muted) + rect(x + 756, 263, 274, 34, '#faf9f8', 8, colors.line) + text(x + 770, 285, '⌕  Cari nama area atau lokasi...', 9, '#aaa4a2') + table(x + 16, 315, 1022, ['Nama area', 'Lokasi', 'Lantai', 'Status', 'Pembaruan', 'Aksi'], [['Ruang Kelas A', 'Gedung A', '1', { badge: 'Aktif', tone: 'green' }, '22 Okt 2026', '•••'], ['Laboratorium', 'Gedung A', '1', { badge: 'Aktif', tone: 'green' }, '22 Okt 2026', '•••'], ['Aula Utama', 'Gedung A', '2', { badge: 'Aktif', tone: 'green' }, '21 Okt 2026', '•••'], ['Toilet Lantai 1', 'Gedung B', '1', { badge: 'Aktif', tone: 'green' }, '21 Okt 2026', '•••'], ['Gudang Belakang', 'Gedung B', '1', { badge: 'Nonaktif', tone: 'amber' }, '18 Okt 2026', '•••'], ['Perpustakaan', 'Gedung A', '2', { badge: 'Aktif', tone: 'green' }, '18 Okt 2026', '•••'], ['Ruang Guru', 'Gedung A', '2', { badge: 'Aktif', tone: 'green' }, '16 Okt 2026', '•••']], { columns: [230, 150, 95, 130, 170, 120], rowHeight: 39 });
            break;
        case 'users':
            body += toolbar();
            body += kpi(x, 138, 'TOTAL PENGGUNA', '18', 'Terdaftar', 'red', 248) + kpi(x + 263, 138, 'ADMIN', '2', 'Pengelola sistem', 'blue', 248) + kpi(x + 526, 138, 'PETUGAS', '16', 'Tim operasional', 'green', 248) + kpi(x + 789, 138, 'AKUN AKTIF', '17', 'Siap digunakan', 'amber', 248);
            body += rect(x, 249, contentWidth, 474, colors.white, 12, colors.line) + text(x + 18, 280, 'Pengguna CleanTrack', 14, colors.ink, 700) + text(x + 18, 299, 'Kelola akses administrator dan petugas kebersihan.', 9, colors.muted) + rect(x + 756, 263, 274, 34, '#faf9f8', 8, colors.line) + text(x + 770, 285, '⌕  Cari nama atau email...', 9, '#aaa4a2') + table(x + 16, 315, 1022, ['Nama', 'Email', 'Peran', 'Status', 'Terakhir aktif', 'Aksi'], [['Nina Safitri', 'nina@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Aktif', tone: 'green' }, 'Hari ini', '•••'], ['Dedi Kurnia', 'dedi@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Aktif', tone: 'green' }, 'Hari ini', '•••'], ['Rina Putri', 'rina@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Aktif', tone: 'green' }, 'Hari ini', '•••'], ['Admin CleanTrack', 'admin@cleantrack.id', { badge: 'Admin', tone: 'red' }, { badge: 'Aktif', tone: 'green' }, 'Hari ini', '•••'], ['Andi Pratama', 'andi@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Nonaktif', tone: 'amber' }, '18 Okt 2026', '•••'], ['Maya Lestari', 'maya@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Aktif', tone: 'green' }, '17 Okt 2026', '•••'], ['Budi Santoso', 'budi@cleantrack.id', { badge: 'Petugas', tone: 'blue' }, { badge: 'Aktif', tone: 'green' }, '16 Okt 2026', '•••']], { columns: [180, 230, 135, 125, 170, 100], rowHeight: 39 });
            break;
        case 'history-admin':
        case 'history-staff':
            body += `${rect(1018, 84, 191, 34, colors.white, 8, colors.line)}${text(1030, 106, '01 Okt  —  22 Okt 2026', 9, colors.muted, 500)}${button(1221, 82, 'Ekspor', false, 103)}`;
            body += kpi(x, 138, 'TOTAL PEMERIKSAAN', screen.kind === 'history-admin' ? '248' : '86', 'Periode terpilih', 'red', 248) + kpi(x + 263, 138, 'BERSIH', screen.kind === 'history-admin' ? '192' : '68', 'Kondisi baik', 'green', 248) + kpi(x + 526, 138, 'PERLU TINDAKAN', screen.kind === 'history-admin' ? '34' : '12', 'Perlu ditindaklanjuti', 'amber', 248) + kpi(x + 789, 138, 'AREA DIPERIKSA', '21', 'Dari 24 area', 'blue', 248);
            body += rect(x, 249, contentWidth, 474, colors.white, 12, colors.line) + text(x + 18, 280, screen.kind === 'history-admin' ? 'Riwayat seluruh monitoring' : 'Riwayat pemeriksaan saya', 14, colors.ink, 700) + text(x + 18, 299, 'Temukan dan tinjau laporan pemeriksaan berdasarkan area dan status.', 9, colors.muted) + rect(x + 687, 263, 343, 34, '#faf9f8', 8, colors.line) + text(x + 701, 285, '⌕  Cari area, petugas, atau catatan...', 9, '#aaa4a2') + table(x + 16, 315, 1022, ['Area', ...(screen.kind === 'history-admin' ? ['Petugas'] : []), 'Kondisi', 'Status', 'Tanggal & waktu', 'Aksi'], screen.kind === 'history-admin' ? [['Ruang Kelas A', 'Nina Safitri', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '22 Okt · 08:42', 'Lihat'], ['Laboratorium', 'Dedi Kurnia', { badge: 'Kotor', tone: 'amber' }, { badge: 'Tindak lanjut', tone: 'amber' }, '22 Okt · 08:15', 'Lihat'], ['Toilet Lantai 1', 'Rina Putri', { badge: 'Sangat kotor', tone: 'red' }, { badge: 'Selesai', tone: 'blue' }, '22 Okt · 07:55', 'Lihat'], ['Aula Utama', 'Nina Safitri', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '21 Okt · 15:10', 'Lihat'], ['Perpustakaan', 'Dedi Kurnia', { badge: 'Cukup bersih', tone: 'blue' }, { badge: 'Selesai', tone: 'green' }, '21 Okt · 14:20', 'Lihat'], ['Ruang Guru', 'Rina Putri', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '21 Okt · 13:18', 'Lihat'], ['Kantin', 'Nina Safitri', { badge: 'Kotor', tone: 'amber' }, { badge: 'Tindak lanjut', tone: 'amber' }, '21 Okt · 12:45', 'Lihat']] : [['Ruang Kelas A', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '22 Okt · 08:42', 'Lihat'], ['Toilet Lantai 1', { badge: 'Sangat kotor', tone: 'red' }, { badge: 'Selesai', tone: 'blue' }, '22 Okt · 07:55', 'Lihat'], ['Aula Utama', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '21 Okt · 15:10', 'Lihat'], ['Perpustakaan', { badge: 'Cukup bersih', tone: 'blue' }, { badge: 'Selesai', tone: 'green' }, '21 Okt · 14:20', 'Lihat'], ['Kantin', { badge: 'Kotor', tone: 'amber' }, { badge: 'Tindak lanjut', tone: 'amber' }, '21 Okt · 12:45', 'Lihat'], ['Laboratorium', { badge: 'Bersih', tone: 'green' }, { badge: 'Selesai', tone: 'green' }, '20 Okt · 10:12', 'Lihat'], ['Ruang Guru', { badge: 'Cukup bersih', tone: 'blue' }, { badge: 'Selesai', tone: 'green' }, '20 Okt · 09:45', 'Lihat']], { columns: screen.kind === 'history-admin' ? [180, 150, 152, 180, 200, 100] : [220, 185, 220, 250, 100], rowHeight: 39 });
            break;
        case 'reports-admin':
        case 'reports-staff':
            body += `${rect(1006, 84, 207, 34, colors.white, 8, colors.line)}${text(1020, 106, 'Bulan ini  ·  Oktober 2026', 9, colors.muted, 500)}${button(1221, 82, 'Unduh laporan', true, 103)}`;
            body += kpi(x, 138, 'TOTAL INSPEKSI', screen.kind === 'reports-admin' ? '248' : '86', 'Bulan ini', 'red', 248) + kpi(x + 263, 138, 'AREA BERSIH', '78%', 'Dari pemeriksaan', 'green', 248) + kpi(x + 526, 138, 'TINDAK LANJUT', screen.kind === 'reports-admin' ? '34' : '12', 'Perlu perhatian', 'amber', 248) + kpi(x + 789, 138, 'PETUGAS AKTIF', screen.kind === 'reports-admin' ? '16' : '1', 'Bulan ini', 'blue', 248);
            body += rect(x, 249, 686, 267, colors.white, 12, colors.line) + cardTitle(x + 18, 278, 'Tren inspeksi', 'Jumlah pemeriksaan kebersihan harian') + chart(x + 23, 317, 625, 142, 1);
            body += rect(x + 702, 249, 352, 267, colors.white, 12, colors.line) + cardTitle(x + 722, 278, 'Ringkasan kondisi', 'Komposisi hasil pemeriksaan') + badge(x + 724, 330, 'Bersih 78%', 'green') + badge(x + 724, 370, 'Cukup bersih 14%', 'blue') + badge(x + 724, 410, 'Perlu tindakan 8%', 'amber') + rect(x + 916, 325, 104, 104, 'none', 52, '#e7e3e2') + `<circle cx="${x + 968}" cy="377" r="48" fill="none" stroke="${colors.green}" stroke-width="16" stroke-dasharray="230 302" transform="rotate(-90 ${x + 968} 377)"/><circle cx="${x + 968}" cy="377" r="48" fill="none" stroke="${colors.amber}" stroke-width="16" stroke-dasharray="24 302" stroke-dashoffset="-234" transform="rotate(-90 ${x + 968} 377)"/>` + text(x + 945, 381, '78%', 15, colors.ink, 700);
            body += rect(x, 532, contentWidth, 190, colors.white, 12, colors.line) + cardTitle(x + 18, 560, 'Area dengan tindak lanjut', 'Area yang memerlukan perhatian lebih lanjut') + table(x + 16, 576, 1022, ['Area', ...(screen.kind === 'reports-admin' ? ['Petugas'] : []), 'Temuan', 'Pemeriksaan terakhir', 'Status'], screen.kind === 'reports-admin' ? [['Laboratorium', 'Dedi Kurnia', 'Peralatan belum tertata', '22 Okt 2026', { badge: 'Diproses', tone: 'amber' }], ['Toilet Lantai 1', 'Rina Putri', 'Perlu pembersihan ulang', '22 Okt 2026', { badge: 'Selesai', tone: 'green' }], ['Kantin', 'Nina Safitri', 'Lantai licin', '21 Okt 2026', { badge: 'Diproses', tone: 'amber' }]] : [['Laboratorium', 'Permukaan perlu dibersihkan', '22 Okt 2026', { badge: 'Diproses', tone: 'amber' }], ['Toilet Lantai 1', 'Perlu pembersihan ulang', '22 Okt 2026', { badge: 'Selesai', tone: 'green' }], ['Kantin', 'Sampah perlu dibuang', '21 Okt 2026', { badge: 'Diproses', tone: 'amber' }]], { columns: screen.kind === 'reports-admin' ? [170, 145, 235, 195, 160] : [190, 310, 245, 180], rowHeight: 27 });
            break;
        case 'profile-admin':
        case 'profile-staff':
            body += rect(x, 138, 327, 576, colors.white, 12, colors.line) + rect(x + 105, 171, 116, 116, '#f4d9dc', 58) + text(x + 137, 242, screen.role === 'ADMIN' ? 'AD' : 'NS', 25, colors.redDark, 700) + text(x + 60, 319, screen.role === 'ADMIN' ? 'Admin CleanTrack' : 'Nina Safitri', 15, colors.ink, 700) + text(x + 105, 342, screen.role === 'ADMIN' ? 'admin@cleantrack.id' : 'nina@cleantrack.id', 9, colors.muted) + badge(x + 118, 375, screen.role === 'ADMIN' ? 'Administrator' : 'Petugas', 'red') + line(x + 22, 411, x + 305, 411) + text(x + 30, 447, 'Bergabung sejak', 9, colors.muted) + text(x + 207, 447, 'Mei 2025', 9, colors.ink, 600) + text(x + 30, 481, 'Status akun', 9, colors.muted) + badge(x + 208, 481, 'Aktif', 'green') + button(x + 50, 526, 'Ganti foto profil', false, 227);
            body += rect(x + 348, 138, 706, 576, colors.white, 12, colors.line) + cardTitle(x + 372, 173, 'Informasi profil', 'Perbarui informasi akun Anda') + field(x + 372, 205, 300, 'NAMA LENGKAP', screen.role === 'ADMIN' ? 'Admin CleanTrack' : 'Nina Safitri') + field(x + 696, 205, 327, 'ALAMAT EMAIL', screen.role === 'ADMIN' ? 'admin@cleantrack.id' : 'nina@cleantrack.id') + field(x + 372, 278, 300, 'NOMOR TELEPON', '+62 812 3456 7890') + field(x + 696, 278, 327, 'PERAN AKUN', screen.role === 'ADMIN' ? 'Administrator' : 'Petugas kebersihan') + line(x + 372, 356, x + 1024, 356) + cardTitle(x + 372, 390, 'Keamanan akun', 'Gunakan kata sandi yang kuat dan unik') + field(x + 372, 418, 300, 'KATA SANDI SAAT INI', '••••••••••') + field(x + 696, 418, 327, 'KATA SANDI BARU', '••••••••••') + field(x + 372, 491, 300, 'KONFIRMASI KATA SANDI', '••••••••••') + button(x + 854, 644, 'Simpan perubahan', true, 169);
            break;
        case 'scan':
            body += `${text(x, 142, 'Pindai kode QR yang terpasang di area ruangan.', 10, colors.muted)}${badge(x + 892, 141, 'Kamera siap', 'green')}`;
            body += rect(x, 159, 700, 552, colors.white, 12, colors.line) + cardTitle(x + 22, 188, 'Scanner QR ruangan', 'Arahkan kamera ke kode QR area') + rect(x + 22, 207, 656, 346, '#242827', 12) + rect(x + 177, 246, 346, 265, '#343b38', 8) + `<path d="M328 279h-27v27 M481 279h27v27 M328 476h-27v-27 M481 476h27v-27" fill="none" stroke="#ffffff" stroke-width="5" stroke-linecap="round"/>` + `<path d="M323 378H486" stroke="${colors.red}" stroke-width="2"/>` + text(x + 268, 535, 'Tempatkan QR di dalam bingkai', 10, colors.white, 500) + button(x + 22, 572, 'Mulai pemindaian', true, 173) + button(x + 209, 572, 'Masukkan kode manual', false, 184);
            body += rect(x + 720, 159, 334, 552, colors.white, 12, colors.line) + cardTitle(x + 740, 188, 'Masukkan kode area', 'Gunakan jika kamera tidak tersedia') + text(x + 740, 232, 'KODE RUANGAN', 9, colors.ink, 600) + rect(x + 740, 245, 294, 44, colors.white, 8, colors.line) + text(x + 756, 273, 'Contoh: CT-AREA-001', 10, '#aaa4a2') + button(x + 740, 304, 'Cari ruangan', true, 294) + line(x + 740, 367, x + 1034, 367) + cardTitle(x + 740, 402, 'Baru-baru ini', 'Ruangan terakhir yang diperiksa') + badge(x + 740, 448, 'Ruang Kelas A', 'green') + text(x + 740, 474, 'Lantai 1  ·  Hari ini, 08:42', 9, colors.muted) + line(x + 740, 491, x + 1034, 491) + badge(x + 740, 527, 'Toilet Lantai 1', 'amber') + text(x + 740, 553, 'Lantai 1  ·  Hari ini, 07:55', 9, colors.muted) + rect(x + 740, 599, 294, 76, colors.redSoft, 9) + text(x + 756, 626, 'Tips pemindaian', 9, colors.redDark, 700) + text(x + 756, 648, 'Pastikan kamera memiliki izin akses.', 8, colors.muted);
            break;
        case 'login':
            body = `${rect(0, 0, 1366, 768, colors.white, 0)}${rect(0, 0, 580, 768, colors.red, 0)}${rect(50, 47, 42, 42, '#ffffff', 12)}${text(61, 75, 'CT', 17, colors.red, 800)}${text(105, 68, 'CLEAN', 19, colors.white, 800)}${text(169, 68, 'TRACK', 19, '#ffd9df', 800)}${text(50, 271, 'Kebersihan terjaga,', 31, colors.white, 700)}${text(50, 311, 'aktivitas makin nyaman.', 31, colors.white, 700)}${text(50, 350, 'Sistem monitoring fasilitas yang praktis dan transparan.', 12, '#ffe9ed', 400)}${rect(50, 400, 410, 1, '#ffffff', 0)}${text(50, 440, '✓  Pantau kebersihan setiap area', 12, colors.white, 500)}${text(50, 477, '✓  Dokumentasi foto dan 360°', 12, colors.white, 500)}${text(50, 514, '✓  Laporan aktivitas secara real-time', 12, colors.white, 500)}${text(50, 710, 'CLEANTRACK  ·  MONITORING KEBERSIHAN', 9, '#ffe9ed', 600)}${text(770, 208, 'Selamat datang kembali', 25, colors.ink, 700)}${text(770, 235, 'Masuk untuk melanjutkan ke dashboard CleanTrack.', 11, colors.muted)}${text(770, 300, 'ALAMAT EMAIL', 9, colors.ink, 600)}${rect(770, 313, 435, 48, colors.white, 9, colors.line)}${text(788, 343, 'nama@cleantrack.id', 11, '#aaa4a2')}${text(770, 397, 'KATA SANDI', 9, colors.ink, 600)}${rect(770, 410, 435, 48, colors.white, 9, colors.line)}${text(788, 440, '••••••••••••', 12, '#aaa4a2')}${text(770, 490, '□  Ingat saya', 10, colors.muted, 500)}${text(1108, 490, 'Lupa kata sandi?', 10, colors.red, 600)}${rect(770, 526, 435, 48, colors.red, 9)}${text(957, 556, 'Masuk', 12, colors.white, 700)}${text(823, 610, 'Akun dikelola oleh administrator fasilitas.', 9, colors.muted, 400)}`;
            return body;
    }
    return body;
}

function makeScreen(screen) {
    const isLogin = screen.kind === 'login';
    return `<svg xmlns="http://www.w3.org/2000/svg" width="1366" height="768" viewBox="0 0 1366 768">
<title>${esc(screen.role)} — ${esc(screen.title)} | CleanTrack</title>
<desc>UI CleanTrack ${esc(screen.title)} untuk role ${esc(screen.role)}. Rancangan SVG dapat diedit setelah diimpor ke Figma.</desc>
<rect width="1366" height="768" fill="${colors.canvas}"/>
${isLogin ? content(screen) : `${shell(screen)}${content(screen)}`}
</svg>`;
}

function makeOverview() {
    const columns = 3;
    const cellWidth = 1366;
    const cellHeight = 768;
    const gapX = 72;
    const gapY = 108;
    const margin = 72;
    const rows = Math.ceil(screens.length / columns);
    const width = margin * 2 + columns * cellWidth + (columns - 1) * gapX;
    const height = margin * 2 + rows * (cellHeight + gapY);
    const frames = screens.map((screen, index) => {
        const col = index % columns;
        const row = Math.floor(index / columns);
        const x = margin + col * (cellWidth + gapX);
        const y = margin + row * (cellHeight + gapY);
        return `<g id="screen-${String(index + 1).padStart(2, '0')}" transform="translate(${x} ${y})">
${text(0, -16, `${String(index + 1).padStart(2, '0')}  ·  ${screen.role}  /  ${screen.title}`, 16, colors.ink, 700)}
${rect(0, 0, cellWidth, cellHeight, colors.white, 14, '#dedbd9')}
${makeScreen(screen).replace(/^<svg[^>]*>[\s\S]*?<rect width="1366" height="768" fill="[^"]*"\/>/, '').replace(/<\/svg>$/, '')}
</g>`;
    }).join('\n');
    return `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
<title>CleanTrack — Complete UI Screens</title>
<desc>Overview 18 layar UI CleanTrack: login, semua menu administrator, semua menu petugas kebersihan, dan profil.</desc>
<rect width="${width}" height="${height}" fill="#eceae9"/>
${frames}
</svg>`;
}

await mkdir(outputDirectory, { recursive: true });
for (const [index, screen] of screens.entries()) {
    const fileName = `${String(index + 1).padStart(2, '0')}-${screen.kind}.svg`;
    await writeFile(join(outputDirectory, fileName), makeScreen(screen), 'utf8');
}
await writeFile(join(outputDirectory, 'cleantrack-overview.svg'), makeOverview(), 'utf8');
console.log(`Generated ${screens.length} screen SVGs and one overview board in ${outputDirectory}`);
