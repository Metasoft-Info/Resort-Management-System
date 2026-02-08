-- Add Customers menu to admin menu settings
INSERT INTO admin_menu_settings (menu_label, menu_icon, route_name, route_pattern, group_name, sort_order, is_active, available_roles, created_at, updated_at)
SELECT 'গ্রাহক', 'fas fa-users', 'admin.customers.index', 'admin.customers.*', 'ব্যবস্থাপনা', 
    (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM admin_menu_settings AS t2), 
    1, '["admin","manager","receptionist"]', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM admin_menu_settings WHERE route_name = 'admin.customers.index'
);
