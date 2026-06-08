document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"][name="gambar"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Hanya kompres gambar (jpeg, png, webp)
            if (!file.type.startsWith('image/')) return;
            
            // Skip jika file sudah cukup kecil (< 300KB)
            if (file.size < 300 * 1024) return;

            try {
                // Tampilkan pesan loading
                let helpText = input.nextElementSibling;
                if (!helpText || !helpText.classList.contains('compress-msg')) {
                    helpText = document.createElement('small');
                    helpText.className = 'text-warning compress-msg d-block mt-1';
                    input.parentNode.insertBefore(helpText, input.nextSibling);
                }
                helpText.textContent = "⏳ Mengompres gambar...";

                // Proses kompresi (max lebar 800px, kualitas 70%)
                const compressedFile = await compressImage(file, 800, 0.7);
                
                // Ganti file di input dengan file yang sudah dikompres menggunakan DataTransfer
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedFile);
                input.files = dataTransfer.files;
                
                // Update pesan
                helpText.className = 'text-success compress-msg d-block mt-1';
                const originalSize = (file.size / 1024).toFixed(0);
                const newSize = (compressedFile.size / 1024).toFixed(0);
                helpText.textContent = `✅ Gambar dikompres (${originalSize}KB → ${newSize}KB) agar proses simpan lebih cepat.`;

            } catch (err) {
                console.error("Gagal mengompres gambar:", err);
                let helpText = input.nextElementSibling;
                if (helpText && helpText.classList.contains('compress-msg')) {
                    helpText.className = 'text-danger compress-msg d-block mt-1';
                    helpText.textContent = "❌ Gagal mengompres gambar, file asli akan digunakan.";
                }
            }
        });
    });

    function compressImage(file, maxWidth, quality) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = event => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round(height * maxWidth / width);
                        width = maxWidth;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Konversi ke JPEG agar ukurannya lebih kecil
                    canvas.toBlob(blob => {
                        if (!blob) {
                            reject(new Error("Canvas toBlob failed"));
                            return;
                        }
                        
                        // Buat nama file baru jika awalnya bukan jpg/jpeg
                        let newName = file.name;
                        if (!newName.toLowerCase().endsWith('.jpg') && !newName.toLowerCase().endsWith('.jpeg')) {
                            newName = newName.substring(0, newName.lastIndexOf('.')) + '.jpg';
                        }

                        const compressedFile = new File([blob], newName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    }, 'image/jpeg', quality);
                };
                img.onerror = error => reject(error);
            };
            reader.onerror = error => reject(error);
        });
    }
});
