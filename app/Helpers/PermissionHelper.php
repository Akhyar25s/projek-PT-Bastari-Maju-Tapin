<?php

if (!function_exists('hasRole')) {
    /**
     * Check apakah user memiliki role tertentu
     */
    function hasRole($role)
    {
        $userRole = session('role');
        $userRoleId = session('id_role');
        
        // Check by role name atau role id
        if (is_array($role)) {
            return in_array($userRole, $role) || in_array($userRoleId, $role);
        }
        
        return $userRole === $role || $userRoleId === $role;
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check apakah user adalah Admin
     */
    function isAdmin()
    {
        $userRole = session('role');
        $userRoleId = session('id_role');
        $idAktor = session('id_aktor');
        
        // Check berdasarkan nama_role
        if ($userRole === 'Admin') {
            return true;
        }
        
        // Check berdasarkan id_role (Admin = id_role '06')
        if ($userRoleId === '06' || $userRoleId == 6) {
            return true;
        }
        
        // Check berdasarkan nama aktor (jika aktor dengan nama 'Admin')
        if ($idAktor == '05' || $idAktor === '05') {
            // Aktor dengan id_aktor '05' adalah Admin
            return true;
        }
        
        return false;
    }
}

if (!function_exists('isDirektur')) {
    /**
     * Check apakah user adalah Direktur
     */
    function isDirektur()
    {
        $userRole = session('role');
        $userRoleId = session('id_role');
        
        return $userRole === 'Direktur' || $userRoleId === '03';
    }
}

if (!function_exists('canView')) {
    /**
     * Check apakah user bisa melihat (Semua role yang login bisa melihat)
     */
    function canView()
    {
        return session('id_aktor') !== null;
    }
}

if (!function_exists('isReadOnly')) {
    /**
     * Check apakah user hanya read-only (Direktur)
     */
    function isReadOnly()
    {
        return isDirektur() && !isAdmin();
    }
}

if (!function_exists('canEdit')) {
    /**
     * Check apakah user bisa edit (Admin saja)
     */
    function canEdit()
    {
        return isAdmin();
    }
}

if (!function_exists('canDelete')) {
    /**
     * Check apakah user bisa delete (Admin saja)
     */
    function canDelete()
    {
        return isAdmin();
    }
}

if (!function_exists('canCreate')) {
    /**
     * Check apakah user bisa create (Admin saja)
     */
    function canCreate()
    {
        return isAdmin();
    }
}

if (!function_exists('canOrder')) {
    /**
     * Check apakah user bisa order (Admin saja)
     */
    function canOrder()
    {
        return isAdmin();
    }
}

