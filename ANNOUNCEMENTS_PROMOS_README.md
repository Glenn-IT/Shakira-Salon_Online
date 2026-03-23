# Announcements & Promos Feature - Setup Guide

## ✅ What's Been Installed

### New Database Tables:

1. **announcements** - Stores announcements for the homepage
2. **promos** - Stores promotional offers with discount information

### New Files:

1. **manage_announcements.php** - Admin panel to manage announcements and promos
2. **add_announcements_promos_tables.sql** - SQL script (already executed)
3. **uploads/promos/** - Folder for promo images

### Updated Files:

1. **index.php** - Now displays announcements and promos on homepage
2. **announcement.php** - Updated navigation menu

## 🚀 How to Use

### For Admins:

#### Access the Management Panel:

1. Login as admin
2. Go to: `http://localhost/Shakira%20Salon_Online/manage_announcements.php`
3. Or click "Announcements & Promos" in the admin sidebar

#### Managing Announcements:

- **Add**: Click "Add Announcement" button
  - Enter title and content
  - Choose type (info/success/warning/danger) for color coding
  - Set status (active/inactive)
- **Edit**: Click the yellow edit button on any announcement
- **Delete**: Click the red trash button

#### Managing Promos:

- **Add**: Click "Add Promo" button
  - Enter promo title and description
  - Set discount (percentage OR amount, not both)
  - Set validity dates (from/until)
  - Optional: Add promo code
  - Optional: Upload promotional image
  - Set status (active/inactive)
- **Edit**: Click the yellow edit button on any promo
- **Delete**: Click the red trash button

### For Clients:

#### On Homepage (`index.php`):

1. **Announcement Bar** - Scrolling announcements at the top (below navbar)
   - Shows all active announcements
   - Auto-scrolling animation
2. **Promos Section** - Displayed between "Our Signature Styles" and "Ready to Shine?"
   - Shows current active promos (valid today)
   - Display promo images, discount badges, and promo codes
   - Hover for more details

## 📋 Features

### Announcements:

- ✅ Four types with color coding (info=blue, success=green, warning=yellow, danger=red)
- ✅ Active/Inactive status control
- ✅ Scrolling marquee display on homepage
- ✅ Timestamps for tracking

### Promos:

- ✅ Percentage discount OR fixed amount discount
- ✅ Validity date range (auto-filters expired promos)
- ✅ Promo codes for tracking
- ✅ Image upload support
- ✅ Active/Inactive status control
- ✅ Beautiful card display with hover effects
- ✅ Auto-displays only valid promos

## 🎨 Design Features

- Modern, responsive card design
- Animated scrolling announcements
- Hover effects on promo cards
- Bootstrap 5 modals for easy management
- Font Awesome icons throughout
- Mobile-friendly layout

## 📝 Sample Data Included

The system comes with sample data:

- 2 announcements (welcome message and new services)
- 2 promos (grand opening sale and weekend special)

You can edit or delete these and add your own!

## 🔧 Troubleshooting

If announcements/promos don't show:

1. Make sure you're logged in as admin
2. Check that announcements/promos are set to "active"
3. For promos, verify the validity dates include today's date
4. Clear browser cache and refresh

## 📱 Navigation Updates

The admin sidebar now has:

- "Business Hours" - The original announcement.php (for managing salon hours)
- "Announcements & Promos" - NEW! manage_announcements.php (for managing messages and deals)

Enjoy your new announcements and promos system! 🎉
