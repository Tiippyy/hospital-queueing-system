# CSS File Structure

The CSS code has been organized into separate files by context and functionality for easier debugging and maintenance:

## File Organization

### 🏗️ **base.css** 
- **Purpose**: Core reset styles and basic typography
- **Contains**: 
  - Global reset (`*` selector)
  - Body and container styles
  - Header typography
  - Base font and color definitions

### 📐 **layout.css**
- **Purpose**: Grid system and layout utilities
- **Contains**:
  - Grid classes (`grid-cols-1` to `grid-cols-4`)
  - Utility classes (`.flex`, `.hidden`, `.text-center`)
  - Responsive grid adjustments
  - Spacing utilities

### 🧭 **navigation.css**
- **Purpose**: Navigation tabs and menu styles
- **Contains**:
  - Tab navigation (`.nav-tabs`, `.nav-btn`)
  - Active/inactive states
  - Hover effects
  - Tab switching animations

### 🃏 **cards.css**
- **Purpose**: Card components and containers
- **Contains**:
  - Card structure (`.card`, `.card-header`, `.card-content`)
  - Card titles and descriptions
  - Card footers and borders
  - Card shadows and spacing

### 📝 **forms.css**
- **Purpose**: Form elements and input styling
- **Contains**:
  - Form groups and labels
  - Input fields (`.form-input`, `.form-select`)
  - Focus states and transitions
  - Textarea styling

### 🔘 **buttons.css**
- **Purpose**: Button styles and interactive elements
- **Contains**:
  - Primary buttons (`.btn`)
  - Button variants (`.btn-sm`, `.btn-outline`, `.btn-danger`)
  - Hover and disabled states
  - Button sizing and spacing

### 🏥 **patient-queue.css**
- **Purpose**: Patient queue specific styling
- **Contains**:
  - Patient cards (`.patient-card`)
  - Urgent priority styling
  - Recommendation tags
  - Patient information layout

### 👨‍⚕️ **doctors.css**
- **Purpose**: Doctor management interface
- **Contains**:
  - Doctor cards and availability indicators
  - Utilization bars and progress
  - Expertise tags
  - Doctor status indicators

### 📋 **patient-history.css**
- **Purpose**: Patient history table and filtering
- **Contains**:
  - History table styling (`.history-table`)
  - Status and priority badges
  - Table hover effects
  - Badge color schemes

### 📊 **reports.css**
- **Purpose**: Reports, analytics, and statistics
- **Contains**:
  - Statistics grid and cards
  - Chart containers
  - Recommendation cards
  - Analytics layouts

### ⏳ **loading.css**
- **Purpose**: Loading states and animations
- **Contains**:
  - Loading spinner (`.spinner`)
  - Loading container styles
  - Spin animation keyframes
  - Loading states

### 📱 **responsive.css**
- **Purpose**: Mobile and responsive design
- **Contains**:
  - Mobile breakpoints (`@media` queries)
  - Responsive adjustments
  - Mobile navigation
  - Tablet and phone optimizations

## Benefits of This Structure

### 🐛 **Easier Debugging**
- **Quick Problem Location**: Button issues → `buttons.css`, Table issues → `patient-history.css`
- **Isolated Testing**: Test individual components without affecting others
- **Clear Responsibility**: Each file has a single, well-defined purpose

### 🔧 **Better Maintenance**
- **Modular Updates**: Change navigation without touching patient styles
- **Component Isolation**: Cards don't interfere with forms
- **Selective Loading**: Load only needed styles for specific features

### 👥 **Team Collaboration**
- **Parallel Development**: Multiple developers can work on different features
- **Clear Ownership**: Each file has obvious responsibility areas
- **Merge Conflicts Reduced**: Changes are isolated to specific files

### 📚 **Improved Organization**
- **Logical Grouping**: Related styles are together
- **Easy Navigation**: Find what you need quickly
- **Scalable Architecture**: Easy to add new feature styles

## Loading Order

The files are loaded in this specific order in `index.php`:

1. **Base Styles**: Foundation styles and reset
2. **Layout**: Grid system and utilities
3. **Components**: Navigation, cards, forms, buttons
4. **Features**: Patient queue, doctors, history, reports
5. **States**: Loading animations
6. **Responsive**: Mobile and tablet adjustments

This ensures proper CSS cascade and prevents style conflicts.

## Usage for Debugging

### 🔍 **Common Issues and Files**
- **Button not styling correctly**: Check `buttons.css`
- **Layout breaking on mobile**: Check `responsive.css`
- **Table formatting issues**: Check `patient-history.css`
- **Navigation not working**: Check `navigation.css`
- **Cards overlapping**: Check `cards.css` and `layout.css`
- **Form inputs not styled**: Check `forms.css`

### 📝 **Adding New Styles**
1. Identify the context (component, feature, or base)
2. Add styles to the appropriate file
3. Follow existing naming conventions
4. Update this documentation

### 🧪 **Testing Styles**
- Temporarily remove specific CSS files to test isolation
- Use browser dev tools to see which file contains specific rules
- Each file can be developed and tested independently

## File Sizes (Approximate)
- `base.css`: ~30 lines - Core foundation
- `layout.css`: ~25 lines - Grid and utilities  
- `navigation.css`: ~25 lines - Tab navigation
- `cards.css`: ~30 lines - Card components
- `forms.css`: ~25 lines - Form elements
- `buttons.css`: ~40 lines - Button styles
- `patient-queue.css`: ~35 lines - Queue features
- `doctors.css`: ~35 lines - Doctor interface
- `patient-history.css`: ~40 lines - History tables
- `reports.css`: ~35 lines - Analytics
- `loading.css`: ~20 lines - Animations
- `responsive.css`: ~15 lines - Mobile styles

**Total**: ~355 lines (same as original, just organized better!)