// KNHS CSS NCII - TESDA Certificate Submission Form Handler
// Program by: Keith Dandan - ICT 12 Magsaysay

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('certificateForm');
    const fileInput = document.getElementById('certificate');
    const dropZone = document.getElementById('dropZone');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const removeFileBtn = document.getElementById('removeFile');
    const submitBtn = document.getElementById('submitBtn');

    // Gmail address for mailto fallback
    const GMAIL_ADDRESS = 'keithcharlespacatangdandan@gmail.com';

    // File Upload Handling
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and Drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect({ target: fileInput });
        }
    });

    // Click to upload
    dropZone.addEventListener('click', function() {
        fileInput.click();
    });

    function handleFileSelect(e) {
        const file = e.target.files[0];
        
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (PNG, JPG, JPEG)');
            resetFile();
            return;
        }

        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('File size must be less than 10MB');
            resetFile();
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            fileName.textContent = file.name;
            dropZone.style.display = 'none';
            filePreview.classList.add('active');
        };
        reader.readAsDataURL(file);
    }

    // Remove file
    removeFileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        resetFile();
    });

    function resetFile() {
        fileInput.value = '';
        previewImage.src = '';
        fileName.textContent = '';
        dropZone.style.display = 'block';
        filePreview.classList.remove('active');
    }

    // Submit via PHP
    async function submitViaPHP(formData) {
        try {
            const response = await fetch('submit.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('PHP Error:', error);
            return { success: false, fallback: true, error: error.message };
        }
    }

    // Build mailto URL for fallback
    function buildMailtoUrl(fullName, email, course, date, message) {
        const subject = encodeURIComponent(`KNHS CSS NCII Certificate Submission - ${fullName}`);
        const body = encodeURIComponent(
            `TESDA Certificate Submission\n\n` +
            `Name: ${fullName}\n` +
            `Email: ${email}\n` +
            `Course: ${course}\n` +
            (date ? `Date: ${date}\n` : '') +
            (message ? `\nMessage: ${message}\n` : '') +
            `\n---\nThe certificate picture is attached to this email.`
        );
        
        return `https://mail.google.com/mail/?view=cm&fs=1&to=${GMAIL_ADDRESS}&su=${subject}&body=${body}`;
    }

    // Form Submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Get form values
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const course = document.getElementById('course').value;
        const certificateDate = document.getElementById('certificateDate').value;
        const message = document.getElementById('message').value.trim();
        const certificateFile = fileInput.files[0];

        // Validation
        if (!fullName || !email || !course || !certificateFile) {
            alert('Please fill in all required fields and upload your certificate.');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<span>Submitting...</span>';

        // Prepare form data
        const formData = new FormData();
        formData.append('fullName', fullName);
        formData.append('email', email);
        formData.append('course', course);
        formData.append('certificateDate', certificateDate);
        formData.append('message', message);
        formData.append('certificate', certificateFile);

        // Try PHP submission first (for automatic attachment)
        const result = await submitViaPHP(formData);

        if (result.success) {
            // PHP submission successful - automatic attachment worked!
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Submitted Successfully!
            `;

            alert(`✅ SUCCESS! Your certificate has been sent!\n\n📧 Check your Gmail: ${GMAIL_ADDRESS}\n\n📎 The certificate photo is ATTACHED to the email!\n\n🎯 Just open your Gmail inbox and you'll see the email with the certificate attached!`);
            
            form.reset();
            resetFile();
            
            // Reset button after 5 seconds
            setTimeout(() => {
                submitBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Submit via Gmail
                `;
            }, 5000);
            
        } else if (result.fallback) {
            // PHP failed, open Gmail with mailto
            submitBtn.innerHTML = '<span>Opening Gmail...</span>';
            
            // Build mailto URL
            const mailtoUrl = buildMailtoUrl(fullName, email, course, certificateDate, message);
            
            // Open Gmail
            window.open(mailtoUrl, '_blank');
            
            // Show instructions
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Submit via Gmail
                `;

                alert(`⚠️ Note: Automatic email failed (${result.message || 'PHP not configured'})\n\nPlease in Gmail:\n1. Attach your certificate: ${certificateFile.name}\n2. Send to: ${GMAIL_ADDRESS}\n\nOr set up PHPMailer for automatic sending:\n1. Run: composer require phpmailer/phpmailer\n2. Generate App Password at myaccount.google.com/apppasswords\n3. Update config/mail-config.php`);
                
                // Reset form
                form.reset();
                resetFile();
            }, 1000);
        } else {
            // Other error
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                Submit via Gmail
            `;
            
            alert(`❌ Error: ${result.message}\n\nPlease try again or contact support.`);
        }
    });

    // Real-time validation feedback
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#ef4444';
            } else {
                this.style.borderColor = '#e2e8f0';
            }
        });

        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#e2e8f0';
            }
        });
    });
});
