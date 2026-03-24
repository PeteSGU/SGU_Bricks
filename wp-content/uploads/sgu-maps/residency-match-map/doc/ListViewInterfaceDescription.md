# UI Specification: Interactive Placement Map (List View)

## Overview
The **List View** provides a detailed, text-based alternative to the Map View for St. George’s University (SGU) residency placements. This interface allows users to view specific program details, including specialties offered and the number of residents matched at each location.

---

## 1. Header & View Controls
* **Page Title:** "Interactive Placement Map" remains constant in the top left.
* **View Toggle:** A segmented control in the top right with "Map View" and "List View" options. 
    * **Active State:** The "List View" button is highlighted , indicating the current view.
* **Total Count:** A status indicator shows "Showing 3 Residency Programs" (likely filtered based on current search parameters). This is displayed on the right to corner bellow the header.

---

## 2. Sidebar Filter Panel (Same as map view)
The left-hand search card remains persistent from the Map View:
* **Heading:** "Search SGU Residencies".
* **Inputs:** Includes a "State or Province" text field with a location icon, and two dropdown menus for "Select Specialty" and "Select Program Name".
* **Buttons:** A teal "EXPLORE" action button and a "Clear All Filters" secondary link.

---

## 3. Results List
The main content area features a vertical stack of program cards. Each card contains:
* **Program Title:** Large, bold navy text (e.g., "Alameda Health System-Highland Hospital Program").
* **Location:** City and State (e.g., "Oakland, CA, USA").
* **Specialties List:** Bold text indicating the medical fields available at that site (e.g., "Emergency Medicine").
* **Match Data:** An orange-highlighted count of "PGY-1 residents matched here" (e.g., "13 PGY-1 residents matched here").
* **Visual Marker:** A small orange dot is located to the left of each program title for visual emphasis.

---

## 4. Footer & Pagination
The bottom of the interface contains navigation tools for large datasets:
* **Result Range:** Text indicating "Showing 1-3 of 12 results".
* **Pagination Controls:** * Left and right directional arrows.
    * Individual page number buttons (1, 2, 3... 12), with "1" highlighted in teal to show the active page.
* **Items Per Page:** A dropdown menu (currently set to "10 per page") allows users to adjust the density of the list.

---

## Technical Layout Notes
* **Card Design:** Each program entry is enclosed in a white card with subtle rounded corners and a light gray border for separation.
* **Consistency:** The UI maintains the navy and teal color scheme consistent with the map-based interface to ensure a seamless user experience.