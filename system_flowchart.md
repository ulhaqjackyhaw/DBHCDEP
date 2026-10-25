# Flowchart Sistem DBHC (Dashboard Kepegawaian Regional 1 PT Angkasa Pura Indonesia)

## 1. Alur Utama Sistem

```mermaid
flowchart TD
    A[User Mengakses Aplikasi] --> B{Sudah Login?}
    B -->|Tidak| C[Halaman Login]
    B -->|Ya| D[Dashboard Utama]
    
    C --> E[Input Username & Password]
    E --> F{Valid?}
    F -->|Tidak| G[Error Message]
    G --> C
    F -->|Ya| H{Role User?}
    
    H -->|Admin| I[Admin Dashboard]
    H -->|User Biasa| D
    
    D --> J[Menu Utama]
    I --> K[Admin Menu]
```

## 2. Struktur Menu dan Hak Akses

```mermaid
flowchart TD
    A[Dashboard Utama] --> B[Analytics]
    A --> C[Data Karyawan]
    A --> D[Formasi]
    A --> E[Realisasi]
    A --> F[Profile]
    A --> G[Version Control]
    A --> H{Admin Only}
    
    H --> I[User Management]
    H --> J[Audit Log]
    
    B --> B1[Analitik Organik]
    B --> B2[Analitik Outsourcing]
    
    C --> C1{User Role?}
    C1 -->|Admin| C2[Full Access]
    C1 -->|User| C3[View Only]
    
    C2 --> C4[Create/Edit/Delete]
    C2 --> C5[Import Data]
    C2 --> C6[Export Data]
    C3 --> C7[View Data]
    C3 --> C6
    
    D --> D1{User Role?}
    D1 -->|Admin| D2[Full Access]
    D1 -->|User| D3[View Only]
    
    D2 --> D4[Create/Edit/Delete]
    D2 --> D5[Import Formasi]
    D2 --> D6[Export Formasi]
    D3 --> D7[View Formasi]
    D3 --> D6
    
    E --> E1{User Role?}
    E1 -->|Admin| E2[Full Access]
    E1 -->|User| E3[View Only]
    
    I --> I1[CRUD Users]
    I --> I2[Manage Roles]
    
    J --> J1[View Audit Trail]
    J --> J2[Filter Logs]
    J --> J3[Track Changes]
```

## 3. Alur Detail Dashboard

```mermaid
flowchart TD
    A[Dashboard Index] --> B[Load KPI Data]
    B --> C[Total Karyawan]
    B --> D[Gender Distribution]
    B --> E[Age Distribution]
    B --> F[Education Level]
    B --> G[Jabatan Lowong]
    
    G --> H[Click Detail Jabatan]
    H --> I{User Role?}
    I -->|Admin| J[View + Export Detail]
    I -->|User| K[View Detail Only]
    
    C --> L[Generate Charts]
    D --> L
    E --> L
    F --> L
    
    L --> M[Interactive Charts dengan ECharts]
    M --> N[Click Chart Elements]
    N --> O[Navigate to Analytics]
```

## 4. Alur Data Karyawan Management

```mermaid
flowchart TD
    A[Data Karyawan Menu] --> B{User Role?}
    B -->|User| C[View Mode]
    B -->|Admin| D[Admin Mode]
    
    C --> E[List Karyawan]
    C --> F[Export Data]
    C --> G[Download Template]
    
    D --> H[Full CRUD Access]
    H --> I[Create New]
    H --> J[Edit Existing]
    H --> K[Delete Record]
    H --> L[Import Operations]
    H --> F
    H --> G
    
    L --> M[Import Add Mode]
    L --> N[Import Replace Mode]
    
    M --> O[Validate Data]
    N --> O
    O --> P{Valid?}
    P -->|Ya| Q[Process Import]
    P -->|Tidak| R[Show Errors]
    R --> L
    Q --> S[Success Message]
```

## 5. Alur Formasi Management

```mermaid
flowchart TD
    A[Formasi Menu] --> B{User Role?}
    B -->|User| C[View Mode]
    B -->|Admin| D[Admin Mode]
    
    C --> E[List Formasi]
    C --> F[Export Data]
    C --> G[Download Template]
    
    D --> H[Full CRUD Access]
    H --> I[Create New Formasi]
    H --> J[Edit Formasi]
    H --> K[Delete Formasi]
    H --> L[Import Operations]
    H --> F
    H --> G
    
    L --> M[Import Add Mode]
    L --> N[Import Replace Mode]
    
    M --> O[Validate Data]
    N --> O
    O --> P{Valid?}
    P -->|Ya| Q[Process Import]
    P -->|Tidak| R[Show Errors]
    R --> L
    Q --> S[Update Jabatan Lowong]
    S --> T[Refresh Dashboard]
```

## 6. Alur Realisasi Management

```mermaid
flowchart TD
    A[Realisasi Menu] --> B{User Role?}
    B -->|User| C[View Mode]
    B -->|Admin| D[Admin Mode]
    
    C --> E[List Realisasi]
    C --> F[Export Data]
    
    D --> G[Full CRUD Access]
    G --> H[Create New]
    G --> I[Edit Existing]
    G --> J[Delete Record]
    G --> F
    
    H --> K[Input Program Data]
    K --> L[Program Kerja, Tahun]
    K --> M[RKAP, Realisasi S1]
    K --> N[Realisasi YTD, Outlook]
    
    L --> O[Validate Input]
    M --> O
    N --> O
    O --> P{Valid?}
    P -->|Ya| Q[Save Realisasi]
    P -->|Tidak| R[Show Errors]
    R --> K
    Q --> S[Log to Audit Trail]
    S --> T[Success Message]
    
    I --> U[Edit Form]
    U --> V[Update Values]
    V --> W[Validate Changes]
    W --> X{Valid?}
    X -->|Ya| Y[Update Record]
    X -->|Tidak| Z[Show Errors]
    Z --> U
    Y --> AA[Log to Audit Trail]
    AA --> AB[Success Message]
```

## 7. Alur Analytics System

```mermaid
flowchart TD
    A[Analytics Menu] --> B[Analitik Organik]
    A --> C[Analitik Outsourcing]
    
    B --> D[Load Organic Data]
    D --> E[Piramida Jabatan per Lokasi]
    D --> F[Gender Distribution per Lokasi]
    D --> G[Age Groups per Lokasi]
    D --> H[Tenure Analysis]
    D --> I[Education Analysis]
    
    E --> J[ECharts Visualization]
    F --> J
    G --> J
    H --> J
    I --> J
    
    J --> K[Interactive Tabs]
    K --> L[Analisis 1 - Demographics]
    K --> M[Analisis 2 - Education]
    K --> N[Analisis 3 - Unit Analysis]
    
    C --> O[Load Outsourcing Data]
    O --> P[Similar Analytics for Outsourcing]
```
    G --> J
    H --> J
    I --> J
    
    J --> K[Interactive Tabs]
    K --> L[Analisis 1 - Demographics]
    K --> M[Analisis 2 - Education]
    K --> N[Analisis 3 - Unit Analysis]
    
    C --> O[Load Outsourcing Data]
    O --> P[Similar Analytics for Outsourcing]
```

## 7. Alur Version Control

```mermaid
flowchart TD
    A[Version Control] --> B[List All Versions]
    B --> C[Create New Version]
    B --> D[Restore Version]
    B --> E[Download Version]
    B --> F[Delete Version]
    
    C --> G[Snapshot Current Data]
    G --> H[Save Version]
    
    D --> I{Confirm Restore?}
    I -->|Ya| J[Restore Data]
    I -->|Tidak| B
    J --> K[Update Database]
    K --> L[Refresh System]
    
    E --> M[Generate Export File]
    F --> N{Confirm Delete?}
    N -->|Ya| O[Delete Version]
    N -->|Tidak| B
```

## 8. Alur User Management (Admin Only)

```mermaid
flowchart TD
    A[User Management] --> B{Admin Access?}
    B -->|Tidak| C[Access Denied]
    B -->|Ya| D[List Users]
    
    D --> E[View User Details]
    D --> F[Create New User]
    D --> G[Edit User]
    D --> H[Delete User]
    
    F --> I[Input User Data]
    I --> J[Name, Email, Password, Role]
    J --> K[Validate Input]
    K --> L{Valid?}
    L -->|Ya| M[Create User]
    L -->|Tidak| N[Show Errors]
    N --> I
    M --> O[Log to Audit Trail]
    O --> P[Success Message]
    
    G --> Q[Edit Form]
    Q --> R[Update Name/Email/Role]
    Q --> S[Change Password - Optional]
    R --> T[Validate Changes]
    S --> T
    T --> U{Valid?}
    U -->|Ya| V[Update User]
    U -->|Tidak| W[Show Errors]
    W --> Q
    V --> X[Log to Audit Trail]
    X --> Y[Success Message]
    
    H --> Z{Confirm Delete?}
    Z -->|Ya| AA[Delete User]
    Z -->|Tidak| D
    AA --> AB[Log to Audit Trail]
    AB --> AC[Success Message]
```

## 9. Alur Audit Log System

```mermaid
flowchart TD
    A[Any Data Change] --> B[Observer Triggered]
    B --> C{Event Type?}
    
    C -->|Created| D[Log Created Event]
    C -->|Updated| E[Log Updated Event]
    C -->|Deleted| F[Log Deleted Event]
    
    D --> G[Capture New Values]
    E --> H[Capture Old & New Values]
    E --> I[Calculate Changes]
    F --> J[Capture Old Values]
    
    G --> K[Create Audit Record]
    H --> K
    I --> K
    J --> K
    
    K --> L[Store Audit Log]
    L --> M[User Info]
    L --> N[IP Address]
    L --> O[User Agent]
    L --> P[Timestamp]
    L --> Q[Model Info]
    L --> R[Changes Detail]
    
    subgraph "Audit Log Viewing"
        S[Admin Access Audit Log] --> T[Filter Options]
        T --> U[Quick Menu Filter]
        T --> V[Advanced Filters]
        
        U --> U1[Data Karyawan]
        U --> U2[Formasi]
        U --> U3[Realisasi]
        U --> U4[User Management]
        
        V --> V1[Search Text]
        V --> V2[Action Type]
        V --> V3[User Filter]
        V --> V4[Date Range]
        
        U1 --> W[Display Results]
        U2 --> W
        U3 --> W
        U4 --> W
        V1 --> W
        V2 --> W
        V3 --> W
        V4 --> W
        
        W --> X[View Details]
        X --> Y[Show Changes]
        Y --> Y1{Field Type?}
        Y1 -->|Sensitive| Y2[Masked Display]
        Y1 -->|Normal| Y3[Show Old/New Values]
    end
```

## 10. Alur Profile Management

```mermaid
flowchart TD
    A[Profile Menu] --> B[View Profile]
    B --> C[Edit Profile Info]
    B --> D[Change Password]
    
    C --> E[Update Name/Email]
    E --> F[Validate Input]
    F --> G{Valid?}
    G -->|Ya| H[Save Changes]
    G -->|Tidak| I[Show Errors]
    I --> C
    H --> J[Success Message]
    
    D --> K[Current Password]
    K --> L[New Password]
    L --> M[Confirm Password]
    M --> N[Validate Passwords]
    N --> O{Valid?}
    O -->|Ya| P[Update Password]
    O -->|Tidak| Q[Show Errors]
    Q --> D
    P --> R[Success Message]
```

## 11. Security & Authentication Flow

```mermaid
flowchart TD
    A[Every Request] --> B[Check Authentication]
    B --> C{Logged In?}
    C -->|Tidak| D[Redirect to Login]
    C -->|Ya| E[Check Route Permissions]
    
    E --> F{Admin Route?}
    F -->|Ya| G{User is Admin?}
    F -->|Tidak| H[Allow Access]
    
    G -->|Ya| H
    G -->|Tidak| I[Access Denied]
    I --> IA[Log Access Attempt]
    
    H --> J[Process Request]
    J --> JA{Data Modification?}
    JA -->|Ya| JB[Trigger Audit Log]
    JA -->|Tidak| K[Return Response]
    JB --> K
    
    D --> L[Login Form]
    L --> M[Submit Credentials]
    M --> N[Validate User]
    N --> O{Valid?}
    O -->|Ya| P[Create Session]
    O -->|Tidak| Q[Login Error]
    P --> R[Redirect to Dashboard]
    Q --> L
```

## 12. Data Flow Architecture

```mermaid
flowchart LR
    A[User Interface] --> B[Routes/Controllers]
    B --> C[Middleware]
    C --> D[Business Logic]
    D --> E[Models/Database]
    
    E --> EA[Audit Log Observer]
    EA --> EB[Track Changes]
    EB --> EC[Store Audit Trail]
    
    E --> F[Data Processing]
    F --> G[Chart Generation]
    G --> H[ECharts Visualization]
    
    E --> I[Export Functions]
    I --> J[Excel/CSV Files]
    
    E --> K[Import Functions]
    K --> L[Data Validation]
    L --> M[Database Updates]
    M --> EA
    
    subgraph "Frontend"
        A
        H
    end
    
    subgraph "Backend"
        B
        C
        D
        E
        EA
        EB
        EC
        F
        G
        I
        J
        K
        L
        M
    end
```

## Penjelasan Sistem:

### **Karakteristik Utama:**
1. **Role-based Access Control**: Admin vs User biasa
2. **Dashboard Interaktif**: Charts dengan ECharts
3. **Data Management**: CRUD operations untuk Karyawan, Formasi, Realisasi
4. **Analytics**: Detailed analysis untuk Organik vs Outsourcing
5. **Version Control**: Snapshot dan restore data
6. **Import/Export**: Excel/CSV processing dengan batch processing
7. **User Management**: Admin dapat mengelola user (Admin only)
8. **Audit Log System**: Automatic tracking semua perubahan data (Admin only)
9. **Security**: Multi-layer authentication & authorization

### **User Roles:**
- **Admin**: Full access - CRUD, Import, Export, Delete, User Management, Audit Log
- **User**: Read access - View, Export only

### **Core Features:**
- Dashboard dengan KPI dan charts interaktif
- Management data karyawan, formasi, dan realisasi
- Analytics mendalam (organik vs outsourcing)
- Version control untuk data backup/restore
- User management dengan role assignment
- Comprehensive audit trail system
- Profile management dengan password change
- Secure authentication system

### **Audit Log Features:**
- **Automatic Tracking**: Semua perubahan (Create, Update, Delete) tercatat otomatis
- **Detailed Information**:
  - User yang melakukan perubahan
  - Timestamp dengan presisi detik
  - IP Address dan User Agent
  - Nilai lama vs nilai baru (field-by-field)
  - Model identifier untuk identifikasi mudah
- **Advanced Filtering**:
  - Quick menu filter (Data Karyawan, Formasi, Realisasi, User)
  - Search text across multiple fields
  - Filter by action type (Created, Updated, Deleted)
  - Filter by user
  - Date range filtering
- **Security Features**:
  - Sensitive fields (password) ditampilkan tapi nilai di-mask
  - Admin-only access
  - Complete audit trail untuk compliance

### **Data Models dengan Audit:**
1. **DataKaryawan** - Employee data tracking
2. **Formasi** - Position formation tracking
3. **Realisasi** - Program realization tracking
4. **User** - User management tracking

### **Technical Stack:**
- **Backend**: Laravel 12, PHP 8.3
- **Database**: MySQL with audit_logs table
- **Frontend**: Blade Templates, Bootstrap 5, Alpine.js
- **Charts**: ECharts for interactive visualizations
- **Excel**: Maatwebsite Excel with batch processing
- **Authentication**: Laravel built-in + Spatie Permissions
- **Audit**: Custom trait-based observer pattern