-- Test data untuk certificates
-- Pastikan user dengan ID 1 ada dan sudah register ke event
-- Insert sample certificate untuk testing

-- Cek apakah ada registrations dengan attended = true
-- UPDATE registrations SET attended = true WHERE user_id = 1 AND id = 1;

-- Insert sample certificate
INSERT INTO certificates (registration_id, certificate_number, certificate_type, file_path, generated_by, generated_at) 
VALUES (
    1, -- registration_id (pastikan ini ada di table registrations)
    'SNIA-2024-PART-001', -- certificate_number
    'participant', -- certificate_type
    'certificates/SNIA_Certificate_2024_001.pdf', -- file_path
    1, -- generated_by (admin user ID)
    NOW() -- generated_at
);

-- Insert another sample certificate
INSERT INTO certificates (registration_id, certificate_number, certificate_type, file_path, generated_by, generated_at) 
VALUES (
    2, -- registration_id
    'SNIA-2024-PRES-001', -- certificate_number  
    'presenter', -- certificate_type
    'certificates/SNIA_Certificate_2024_002.pdf', -- file_path
    1, -- generated_by
    NOW() -- generated_at
);