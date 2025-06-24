# App LMS

## Cara Membuat Width Fit-Content pada DataTable di Laravel

Berikut adalah beberapa cara untuk membuat width fit-content pada DataTable di Laravel:

### 1. Menggunakan `autoWidth: false` dan CSS

```javascript
var table = $("#class-table").DataTable({
    // ... konfigurasi lainnya
    autoWidth: false, // Set false untuk fit-content
    columns: [
        {
            data: "id",
            width: "50px", // Set width untuk setiap kolom
        },
        {
            data: "name",
            width: "200px",
        },
        // ... kolom lainnya
    ],
    initComplete: function () {
        // Set table width to fit content
        $(this).find("table").css("width", "fit-content");
    },
});
```

### 2. Menambahkan CSS untuk fit-content

Tambahkan CSS berikut ke file `resources/css/app.css`:

```css
/* DataTable Fit Content Styles */
.fit-content-datatable {
    width: fit-content !important;
    max-width: 100%;
}

.fit-content-datatable table {
    width: fit-content !important;
    min-width: auto !important;
}

.fit-content-datatable .dataTables_wrapper {
    width: fit-content !important;
}

.fit-content-datatable .dataTables_scroll {
    width: fit-content !important;
}

/* Responsive fit-content */
@media (max-width: 768px) {
    .fit-content-datatable {
        width: 100% !important;
    }

    .fit-content-datatable table {
        width: 100% !important;
    }

    .fit-content-datatable .dataTables_wrapper {
        width: 100% !important;
    }
}
```

### 3. Menggunakan columnDefs untuk mengatur width yang lebih fleksibel

```javascript
var table = $("#class-table").DataTable({
    // ... konfigurasi lainnya
    columnDefs: [
        {
            targets: 0, // Kolom checkbox
            width: "50px",
            className: "text-center",
        },
        {
            targets: 1, // Kolom nama
            width: "200px",
            className: "text-left",
        },
        // ... definisi kolom lainnya
    ],
    autoWidth: false,
    initComplete: function () {
        setFitContent($(this));
    },
});
```

### 4. Membuat fungsi helper untuk fit-content

```javascript
// Helper function untuk mengatur fit-content
function setFitContent(table) {
    const wrapper = table.closest(".dataTables_wrapper");
    const tableElement = table.find("table");

    if (isFitContentMode) {
        // Set width fit-content
        wrapper.addClass("fit-content-datatable");
        tableElement.css("width", "fit-content");
    } else {
        // Set width 100%
        wrapper.removeClass("fit-content-datatable");
        tableElement.css("width", "100%");
    }

    // Recalculate column widths
    table.columns.adjust();
}

// Helper function untuk responsive behavior
function handleResponsive() {
    const windowWidth = $(window).width();
    const wrapper = $(".fit-content-datatable");

    if (windowWidth <= 768) {
        wrapper.removeClass("fit-content-datatable");
        wrapper.find("table").css("width", "100%");
    } else if (isFitContentMode) {
        wrapper.addClass("fit-content-datatable");
        wrapper.find("table").css("width", "fit-content");
    }
}
```

### 5. Menambahkan tombol toggle untuk fit-content

Tambahkan tombol di view:

```html
<button class="btn btn-success f-w-500 mb-2 ms-2" id="toggle-fit-content">
    <i class="fa fa-expand pe-2"></i>Mode Full Width
</button>
```

Dan JavaScript untuk handle toggle:

```javascript
// Function untuk toggle fit-content mode
function toggleFitContent() {
    isFitContentMode = !isFitContentMode;
    setFitContent(t);

    // Update tombol toggle
    const toggleBtn = $("#toggle-fit-content");
    if (isFitContentMode) {
        toggleBtn
            .text("Mode Full Width")
            .removeClass("btn-success")
            .addClass("btn-warning");
    } else {
        toggleBtn
            .text("Mode Fit Content")
            .removeClass("btn-warning")
            .addClass("btn-success");
    }
}

// Event listener untuk tombol toggle fit-content
$(document).on("click", "#toggle-fit-content", function () {
    toggleFitContent();
});
```

### 6. Event listener untuk responsive behavior

```javascript
// Event listener untuk responsive behavior
$(window).on("resize", function () {
    handleResponsive();
    table.columns.adjust();
});
```

### Fitur yang Tersedia:

1. **Mode Fit Content**: Tabel akan menyesuaikan lebar dengan konten
2. **Mode Full Width**: Tabel akan menggunakan lebar penuh
3. **Responsive**: Otomatis beralih ke full width pada layar kecil
4. **Toggle Button**: Tombol untuk beralih antara mode fit-content dan full width
5. **Column Width Control**: Kontrol lebar setiap kolom secara individual

### Catatan Penting:

-   Pastikan `autoWidth: false` diset untuk mengontrol width secara manual
-   Gunakan `columnDefs` untuk mengatur width dan class setiap kolom
-   Tambahkan event listener untuk responsive behavior
-   Gunakan CSS untuk styling tambahan
-   Test pada berbagai ukuran layar untuk memastikan responsivitas

### File yang Dimodifikasi:

1. `public/assets/js/class-crud.js` - JavaScript untuk DataTable
2. `resources/css/app.css` - CSS untuk styling fit-content
3. `resources/views/user/class/index.blade.php` - View dengan tombol toggle

Dengan implementasi ini, DataTable Anda akan memiliki width fit-content yang responsif dan dapat di-toggle sesuai kebutuhan.
