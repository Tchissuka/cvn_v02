SET NAMES utf8mb4;
SET collation_connection = 'utf8mb4_general_ci';
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS clinic_id BIGINT NULL AFTER last_login_at;

CREATE TABLE IF NOT EXISTS general_numerator (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    type_num_id INT NOT NULL,
    mode ENUM('A','C') NOT NULL DEFAULT 'A',
    number BIGINT NOT NULL DEFAULT 0,
    number_year INT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

SET @SUBMENU_OFFSET := 100000;
SET @USER_MENU_GROUP_OFFSET := 500000;
SET @CLINIC_UNASSIGNED_ID := 900000001;
SET @CLINIC_GLOBAL_ID := 900000002;

INSERT INTO clinics (id, name, code, tax_id, email, phone, address, city, country, created_at, updated_at)
VALUES
    (@CLINIC_UNASSIGNED_ID, 'Sem clinica atribuida', 'legacy-unassigned', NULL, NULL, NULL, NULL, NULL, 'Angola', NOW(), NOW()),
    (@CLINIC_GLOBAL_ID, 'Catalogo legado global', 'legacy-global', NULL, NULL, NULL, NULL, NULL, 'Angola', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    code = VALUES(code),
    updated_at = NOW();

INSERT INTO clinics (id, name, code, tax_id, email, phone, address, city, country, created_at, updated_at)
SELECT
    i.Id,
    TRIM(COALESCE(NULLIF(i.instituicaoName, ''), CONCAT('Instituicao ', i.Id))),
    COALESCE(NULLIF(TRIM(i.breviat), ''), CONCAT('legacy-inst-', i.Id)),
    NULLIF(TRIM(i.nifInst), ''),
    CASE WHEN TRIM(COALESCE(i.email, '')) LIKE '%@%' THEN LOWER(TRIM(i.email)) ELSE NULL END,
    NULLIF(TRIM(i.telefone), ''),
    NULLIF(TRIM(i.address), ''),
    NULL,
    'Angola',
    COALESCE(i.created_at, NOW()),
    i.updated_at
FROM __SOURCE_DB__.institutions i
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    code = VALUES(code),
    tax_id = VALUES(tax_id),
    email = VALUES(email),
    phone = VALUES(phone),
    address = VALUES(address),
    country = VALUES(country),
    updated_at = VALUES(updated_at);

INSERT INTO roles (id, name, slug, description, created_at, updated_at)
SELECT
    t.Id,
    TRIM(COALESCE(NULLIF(t.name, ''), CONCAT('Perfil ', t.Id))),
    CONCAT('legacy-role-', t.Id),
    'Migrado de cvnsu_bd.user_type',
    COALESCE(t.creted_at, NOW()),
    t.updated_at
FROM __SOURCE_DB__.user_type t
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    updated_at = VALUES(updated_at);

INSERT INTO menu_groups (id, name, slug, description, is_default, created_at, updated_at)
SELECT
    t.Id,
    TRIM(COALESCE(NULLIF(t.name, ''), CONCAT('Grupo ', t.Id))),
    CONCAT('legacy-group-', t.Id),
    'Grupo base migrado de cvnsu_bd.user_type',
    1,
    COALESCE(t.creted_at, NOW()),
    t.updated_at
FROM __SOURCE_DB__.user_type t
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    is_default = VALUES(is_default),
    updated_at = VALUES(updated_at);

INSERT INTO menus (id, name, slug, icon, route, parent_id, sort_order, is_active, created_by, created_at, updated_at)
SELECT
    m.Id,
    TRIM(COALESCE(NULLIF(m.name_menu, ''), CONCAT('Menu ', m.Id))),
    CONCAT('legacy-menu-', m.Id),
    COALESCE(NULLIF(TRIM(m.function_n), ''), 'fas fa-folder'),
    NULL,
    NULL,
    m.Id,
    1,
    m.created_by,
    COALESCE(m.created_at, NOW()),
    m.updated_at
FROM __SOURCE_DB__.main_menu m
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    icon = VALUES(icon),
    sort_order = VALUES(sort_order),
    updated_at = VALUES(updated_at);

INSERT INTO menus (id, name, slug, icon, route, parent_id, sort_order, is_active, created_by, created_at, updated_at)
SELECT
    @SUBMENU_OFFSET + s.Id,
    TRIM(COALESCE(NULLIF(s.nomeSubmenu, ''), CONCAT('Submenu ', s.Id))),
    CONCAT('legacy-submenu-', s.Id),
    COALESCE(NULLIF(TRIM(m.function_n), ''), 'fas fa-circle'),
    CONCAT('/', TRIM(LEADING '/' FROM REPLACE(COALESCE(s.caminho, ''), '\\', '/'))),
    s.idPr,
    s.Id,
    1,
    NULL,
    COALESCE(s.created_at, NOW()),
    s.update_at
FROM __SOURCE_DB__.submenus s
LEFT JOIN __SOURCE_DB__.main_menu m ON m.Id = s.idPr
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    icon = VALUES(icon),
    route = VALUES(route),
    parent_id = VALUES(parent_id),
    sort_order = VALUES(sort_order),
    updated_at = VALUES(updated_at);

INSERT IGNORE INTO menu_group_items (menu_group_id, menu_id, sort_order, created_by, created_at, updated_at)
SELECT
    gm.id_grup,
    @SUBMENU_OFFSET + gm.id_subMen,
    gm.id_subMen,
    gm.created_by,
    COALESCE(gm.created_at, NOW()),
    gm.update_at
FROM __SOURCE_DB__.group_menus gm;

INSERT INTO menu_groups (id, name, slug, description, is_default, created_at, updated_at)
SELECT
    @USER_MENU_GROUP_OFFSET + u.Id,
    CONCAT('Acesso pessoal - ', COALESCE(NULLIF(TRIM(p.full_name), ''), u.user_name)),
    CONCAT('legacy-user-', u.Id),
    'Grupo pessoal criado a partir de user_submenu',
    0,
    COALESCE(u.created_at, NOW()),
    u.updated_at
FROM __SOURCE_DB__.users u
LEFT JOIN __SOURCE_DB__.personal p ON p.Id = u.Id
WHERE EXISTS (
    SELECT 1
    FROM __SOURCE_DB__.user_submenu us
    WHERE us.idPerson = u.Id
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    updated_at = VALUES(updated_at);

INSERT IGNORE INTO menu_group_items (menu_group_id, menu_id, sort_order, created_by, created_at, updated_at)
SELECT
    @USER_MENU_GROUP_OFFSET + us.idPerson,
    @SUBMENU_OFFSET + us.idSubmen,
    us.idSubmen,
    us.createdBy,
    COALESCE(us.created_at, NOW()),
    us.updated_at
FROM __SOURCE_DB__.user_submenu us;

INSERT INTO people (
    id,
    clinic_id,
    first_name,
    last_name,
    full_name,
    birth_date,
    gender,
    tax_id,
    document_number,
    phone,
    mobile,
    email,
    address,
    city,
    country,
    notes,
    created_at,
    updated_at
)
SELECT
    p.Id,
    COALESCE(NULLIF(e.id_institui, 0), NULLIF(pa2.clinic_id, 0), NULLIF(u.instit, 0), @CLINIC_UNASSIGNED_ID),
    TRIM(SUBSTRING_INDEX(TRIM(p.full_name), ' ', 1)),
    NULLIF(TRIM(SUBSTRING(TRIM(p.full_name), LENGTH(SUBSTRING_INDEX(TRIM(p.full_name), ' ', 1)) + 1)), ''),
    TRIM(p.full_name),
    p.birth_date,
    CASE
        WHEN p.genre = 'M' THEN 'male'
        WHEN p.genre = 'F' THEN 'female'
        ELSE NULL
    END,
    NULLIF(TRIM(p.contribuit), ''),
    NULLIF(TRIM(p.identity_card), ''),
    NULLIF(TRIM(CAST(ad.telefon_1 AS CHAR)), ''),
    NULLIF(TRIM(CAST(ad.telefon_2 AS CHAR)), ''),
    CASE WHEN TRIM(COALESCE(ad.email, '')) LIKE '%@%' THEN LOWER(TRIM(ad.email)) ELSE NULL END,
    NULLIF(TRIM(ad.Street), ''),
    NULL,
    NULLIF(TRIM(p.nacional), ''),
    CONCAT_WS(' | ',
        CASE WHEN NULLIF(TRIM(p.father_name), '') IS NOT NULL THEN CONCAT(_utf8mb4'Pai: ', CONVERT(TRIM(p.father_name) USING utf8mb4)) END,
        CASE WHEN NULLIF(TRIM(p.mother_name), '') IS NOT NULL THEN CONCAT(_utf8mb4'Mae: ', CONVERT(TRIM(p.mother_name) USING utf8mb4)) END,
        CASE WHEN NULLIF(TRIM(p.place_of_issue), '') IS NOT NULL THEN CONCAT(_utf8mb4'Local doc: ', CONVERT(TRIM(p.place_of_issue) USING utf8mb4)) END,
        CASE WHEN p.date_of_issue IS NOT NULL THEN CONCAT(_utf8mb4'Data doc: ', DATE_FORMAT(p.date_of_issue, '%Y-%m-%d')) END,
        CASE WHEN NULLIF(TRIM(CAST(ad.emergency_phone AS CHAR)), '') IS NOT NULL THEN CONCAT(_utf8mb4'Emergencia: ', CONVERT(TRIM(COALESCE(ad.emergency_name, '')) USING utf8mb4), _utf8mb4' / ', TRIM(CAST(ad.emergency_phone AS CHAR))) END,
        CASE WHEN NULLIF(TRIM(ad.occupation_profession), '') IS NOT NULL THEN CONCAT(_utf8mb4'Profissao: ', CONVERT(TRIM(ad.occupation_profession) USING utf8mb4)) END,
        CONCAT(_utf8mb4'legacy_person_id: ', p.Id)
    ),
    COALESCE(p.created_at, NOW()),
    p.updated_at
FROM __SOURCE_DB__.personal p
LEFT JOIN __SOURCE_DB__.personal_address ad ON ad.personal_id = p.Id
LEFT JOIN __SOURCE_DB__.employees e ON e.id_person = p.Id
LEFT JOIN __SOURCE_DB__.patients pa2 ON pa2.person_id = p.Id
LEFT JOIN __SOURCE_DB__.users u ON u.Id = p.Id
ON DUPLICATE KEY UPDATE
    clinic_id = VALUES(clinic_id),
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    full_name = VALUES(full_name),
    birth_date = VALUES(birth_date),
    gender = VALUES(gender),
    tax_id = VALUES(tax_id),
    document_number = VALUES(document_number),
    phone = VALUES(phone),
    mobile = VALUES(mobile),
    email = VALUES(email),
    address = VALUES(address),
    country = VALUES(country),
    notes = VALUES(notes),
    updated_at = VALUES(updated_at);

INSERT INTO users (id, name, email, password_hash, is_active, last_login_at, clinic_id, created_at, updated_at)
SELECT
    u.Id,
    COALESCE(NULLIF(TRIM(p.full_name), ''), u.user_name),
    CONCAT('legacy-user-', u.Id, '@migrated.local'),
    u.passwd,
    CASE WHEN COALESCE(u.estatutoUtiliza, 'A') = 'E' THEN 0 ELSE 1 END,
    COALESCE(u.dataUltimoAcesso, u.updated_at, u.created_at),
    COALESCE(NULLIF(u.instit, 0), @CLINIC_UNASSIGNED_ID),
    COALESCE(u.created_at, NOW()),
    u.updated_at
FROM __SOURCE_DB__.users u
LEFT JOIN __SOURCE_DB__.personal p ON p.Id = u.Id
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    password_hash = VALUES(password_hash),
    is_active = VALUES(is_active),
    last_login_at = VALUES(last_login_at),
    clinic_id = VALUES(clinic_id),
    updated_at = VALUES(updated_at);

INSERT IGNORE INTO role_user (user_id, role_id, created_at, updated_at)
SELECT
    u.Id,
    u.tipoUtili,
    COALESCE(u.created_at, NOW()),
    u.updated_at
FROM __SOURCE_DB__.users u
WHERE u.tipoUtili IS NOT NULL;

INSERT IGNORE INTO user_menu_groups (user_id, menu_group_id, created_at, updated_at)
SELECT
    u.Id,
    u.tipoUtili,
    COALESCE(u.created_at, NOW()),
    u.updated_at
FROM __SOURCE_DB__.users u
WHERE u.tipoUtili IS NOT NULL;

INSERT IGNORE INTO user_menu_groups (user_id, menu_group_id, created_at, updated_at)
SELECT
    u.Id,
    @USER_MENU_GROUP_OFFSET + u.Id,
    COALESCE(u.created_at, NOW()),
    u.updated_at
FROM __SOURCE_DB__.users u
WHERE EXISTS (
    SELECT 1
    FROM __SOURCE_DB__.user_submenu us
    WHERE us.idPerson = u.Id
);

INSERT INTO employees (
    id,
    clinic_id,
    person_id,
    department_id,
    job_title,
    hire_date,
    termination_date,
    is_active,
    created_by,
    created_at,
    updated_at
)
SELECT
    e.Id,
    COALESCE(NULLIF(e.id_institui, 0), p.clinic_id, @CLINIC_UNASSIGNED_ID),
    e.id_person,
    NULL,
    COALESCE(NULLIF(TRIM(e.bond), ''), CASE WHEN e.id_category IS NOT NULL THEN CONCAT('Categoria legado ', e.id_category) END, 'Funcionario'),
    e.start_date,
    NULL,
    CASE
        WHEN UPPER(COALESCE(e.status, 'ACTIVO')) IN ('INACTIVO', 'INATIVO', 'I', 'N', '0') THEN 0
        ELSE 1
    END,
    e.created_by,
    COALESCE(e.created_at, NOW()),
    e.updated_at
FROM __SOURCE_DB__.employees e
INNER JOIN people p ON p.id = e.id_person
ON DUPLICATE KEY UPDATE
    clinic_id = VALUES(clinic_id),
    person_id = VALUES(person_id),
    job_title = VALUES(job_title),
    hire_date = VALUES(hire_date),
    is_active = VALUES(is_active),
    updated_at = VALUES(updated_at);

INSERT INTO patients (
    id,
    clinic_id,
    person_id,
    medical_record_number,
    insurance_id,
    notes,
    created_at,
    updated_at
)
SELECT
    pa.Id,
    COALESCE(NULLIF(pa.clinic_id, 0), p.clinic_id, @CLINIC_UNASSIGNED_ID),
    pa.person_id,
    COALESCE(NULLIF(TRIM(pa.regist_number), ''), CONCAT('LEGACY-PAT-', pa.Id)),
    NULL,
    CONCAT('legacy_patient_id: ', pa.Id),
    COALESCE(pa.created_at, NOW()),
    pa.updated_at
FROM __SOURCE_DB__.patients pa
INNER JOIN people p ON p.id = pa.person_id
ON DUPLICATE KEY UPDATE
    clinic_id = VALUES(clinic_id),
    person_id = VALUES(person_id),
    medical_record_number = VALUES(medical_record_number),
    notes = VALUES(notes),
    updated_at = VALUES(updated_at);

INSERT INTO general_numerator (
    id,
    clinic_id,
    type_num_id,
    mode,
    number,
    number_year,
    created_at,
    updated_at
)
SELECT
    gn.Id,
    COALESCE(NULLIF(gn.clinic_id, 0), @CLINIC_UNASSIGNED_ID),
    COALESCE(gn.type_num_id, 0),
    'A',
    COALESCE(gn.number, 0),
    COALESCE(gn.number_year, YEAR(COALESCE(gn.created_at, NOW()))),
    COALESCE(gn.created_at, NOW()),
    gn.updated_at
FROM __SOURCE_DB__.general_numerator gn
ON DUPLICATE KEY UPDATE
    clinic_id = VALUES(clinic_id),
    type_num_id = VALUES(type_num_id),
    mode = VALUES(mode),
    number = VALUES(number),
    number_year = VALUES(number_year),
    updated_at = VALUES(updated_at);

INSERT INTO product_categories (
    id,
    clinic_id,
    name,
    code,
    description,
    created_at,
    updated_at
)
SELECT
    pc.Id,
    @CLINIC_GLOBAL_ID,
    TRIM(COALESCE(NULLIF(pc.categori, ''), CONCAT('Categoria ', pc.Id))),
    CONCAT('LEGACY-PCAT-', pc.Id),
    NULLIF(TRIM(pc.description), ''),
    COALESCE(pc.created_at, NOW()),
    pc.updated_at
FROM __SOURCE_DB__.categorie_poducts pc
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    updated_at = VALUES(updated_at);

INSERT INTO products (
    id,
    clinic_id,
    name,
    code,
    barcode,
    description,
    unit,
    cost_price,
    sale_price,
    is_active,
    created_by,
    created_at,
    updated_at
)
SELECT
    pr.Id,
    @CLINIC_GLOBAL_ID,
    TRIM(COALESCE(NULLIF(pr.name_product, ''), CONCAT('Produto ', pr.Id))),
    COALESCE(NULLIF(TRIM(pr.internal_code), ''), CONCAT('LEGACY-PROD-', pr.Id)),
    NULLIF(TRIM(pr.barcode), ''),
    CONCAT_WS(' | ',
        CASE WHEN NULLIF(TRIM(pr.detal_product), '') IS NOT NULL THEN CONVERT(TRIM(pr.detal_product) USING utf8mb4) END,
        CASE WHEN NULLIF(TRIM(pr.image), '') IS NOT NULL THEN CONCAT(_utf8mb4'Imagem legado: ', CONVERT(TRIM(pr.image) USING utf8mb4)) END
    ),
    CASE
        WHEN pr.type_measure = 'K' THEN 'KG'
        WHEN pr.type_measure = 'L' THEN 'L'
        WHEN pr.type_measure = 'M' THEN 'M'
        ELSE 'UN'
    END,
    COALESCE(pr.cust_price, 0.00),
    COALESCE(pr.price, 0.00),
    1,
    pr.created_by,
    COALESCE(pr.created_at, NOW()),
    pr.updated_at
FROM __SOURCE_DB__.products pr
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    code = VALUES(code),
    barcode = VALUES(barcode),
    description = VALUES(description),
    unit = VALUES(unit),
    cost_price = VALUES(cost_price),
    sale_price = VALUES(sale_price),
    updated_at = VALUES(updated_at);

INSERT IGNORE INTO product_category_product (
    clinic_id,
    product_id,
    product_category_id,
    created_at,
    updated_at
)
SELECT
    @CLINIC_GLOBAL_ID,
    pr.Id,
    pr.category_id,
    COALESCE(pr.created_at, NOW()),
    pr.updated_at
FROM __SOURCE_DB__.products pr
INNER JOIN product_categories pc ON pc.id = pr.category_id;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'clinics' AS entity, COUNT(*) AS total FROM clinics
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'roles', COUNT(*) FROM roles
UNION ALL SELECT 'people', COUNT(*) FROM people
UNION ALL SELECT 'employees', COUNT(*) FROM employees
UNION ALL SELECT 'patients', COUNT(*) FROM patients
UNION ALL SELECT 'menus', COUNT(*) FROM menus
UNION ALL SELECT 'menu_groups', COUNT(*) FROM menu_groups
UNION ALL SELECT 'products', COUNT(*) FROM products
UNION ALL SELECT 'product_categories', COUNT(*) FROM product_categories;