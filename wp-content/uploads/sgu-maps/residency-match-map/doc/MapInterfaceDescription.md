# UI Specification: Interactive Placement Map

## Overview
The **Interactive Placement Map** is a data visualization tool designed for St. George’s University (SGU) to display residency placement locations across North America. The interface uses a geospatial layout to help users identify geographic trends in medical residency matches.

---

## 1. Visual Theme
* **Color Palette:** * **Primary:** Deep Navy (#1A1B4B) for headers and filter cards.
    * **Accent:** Teal/Cyan (#A7E9E7) for primary action buttons.
    * **Background:** Sky Blue for water and Light Gray/White for landmasses.
* **Typography:** Modern, geometric sans-serif font.
* **Styling:** Rounded corners on UI elements (cards, buttons) and high-contrast data markers.

---

## 2. Header Components
* **Dashboard Title:** "Interactive Placement Map" (Top Left).
* **View Toggle:** A segmented control (Top Right) allowing users to switch between:
    * **Map View:** (Active) Spatial distribution.
    * **List View:** Tabular format of the programs.

---

## 3. Navigation & Filtering Panel
Located as a floating card on the left side of the viewport:
* **Search Bar:** "State or Province" input with a GPS/location icon.
* **Specialty Filter:** Dropdown menu to filter by medical field (e.g., Surgery, Pediatrics).
* **Program Name Filter:** Dropdown menu to search for specific hospitals or institutions.
* **Primary Action:** "EXPLORE" button (Teal).
* **Secondary Action:** "Clear All Filters" text button.

---

## 4. Map Interface
The core component is an interactive SVG or Tile-based map.

### Data Visualization
* **Clustered Markers:** Dark navy circles with white numerical labels. These represent the number of residency programs in a specific geographic cluster.
* **Example Densities:**
    * **Northeast Corridor:** 570
    * **Pennsylvania Area:** 202
    * **Michigan Area:** 124
    * **Texas:** 36

### Map Utilities
* **Dynamic Counter:** "Showing 1,049 Residency Programs" displayed in a white pill-box.
* **Search This Area:** A floating button at the top-center to refresh data based on the current zoom level.
* **Zoom Controls:** Standard `+` and `-` buttons with a "Fit to Screen" toggle in the top-right corner.

---

## 5. Interaction Model
1.  **Zoom/Pan:** Users can navigate the map to see specific state-level data.
2.  **Filter:** Updating the "Specialty" dropdown dynamically reduces the counts shown on the map markers.
3.  **Hover/Click:** Typically, clicking a cluster (e.g., "570") would zoom into that region or open a sidebar with specific program details.

---

## Technical Considerations
* **Responsive Design:** The filter card is positioned to overlap the map, suggesting a mobile-friendly "drawer" or "overlay" pattern.
* **Accessibility:** High contrast between the navy markers and the light-colored map background.