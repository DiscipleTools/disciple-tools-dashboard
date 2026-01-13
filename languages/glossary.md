# Disciple Tools Dashboard Plugin Glossary

A comprehensive glossary of terms, concepts, and domain-specific vocabulary used in the Disciple Tools Dashboard plugin.

---

## Core Dashboard Concepts

**Dashboard** - The main landing page that assists disciple makers in knowing the most important actions to take. Displays tiles with key metrics and contact information.

**Tile** - A modular component/widget displayed on the dashboard. Each tile represents a specific data view or interaction area. Tiles can be shown, hidden, reordered, and customized.

**Handle** - The unique identifier for a tile (e.g., `DT_Dashboard_Plugin_Active_Contact`). Used internally to reference tiles in the system.

**Label** - The user-facing display name for a tile (e.g., "Active Contacts", "Tasks").

**Priority** - A numeric value determining the default display order of tiles on the dashboard. Lower numbers appear first.

**Span** - The width/grid size of a tile (values 1-4). Determines how much horizontal space the tile occupies on the dashboard layout.

**Visibility Toggle** - The ability to show or hide tiles from the dashboard view. Users can customize which tiles appear on their personal dashboard.

**Tile Sort** - The custom ordering of tiles on the dashboard. Tiles can be reordered via drag-and-drop interface.

---

## Contact-Related Terms

**Contact** - An individual person tracked in the Disciple.Tools system who is being followed up with.

**Active Contact** - A contact with an `overall_status` of "active" that is assigned to the current user. These are contacts currently being worked with.

**Assigned To** - The relationship indicating which user a contact is assigned to. Used to filter contacts to show only those "assigned to me".

**Pending Contacts** - Contacts that have been assigned to a user but have not yet been accepted. Also called "Needs Accepting". Require an accept/decline action.

**Update Needed** - Contacts that require follow-up because the `requires_update` field is marked as true. Shows contacts that haven't been updated for a specified period.

**Contact Workload** - Indicates the current capacity or availability status of a user for accepting new contacts. Options include "active", "existing", and "inactive".

**Access Ministry** - The type of ministry context where there is a large amount of incoming contacts that need follow-up. The dashboard helps prioritize these urgent issues.

---

## Seeker/Disciple Progress Terms

**Seeker Path** - The progression stage of a contact showing their spiritual journey. Includes multiple stages showing where contacts are in their faith development.

**Seeker Path Progress** - A tile displaying a pie chart showing the distribution of the current user's active contacts across different seeker path stages.

**Seeker Path Personal** - The user's personal contacts data broken down by seeker path stages. Retrieved via the `/seeker_path_personal` REST endpoint.

**Met** - A seeker path value indicating the contact has been met in person (tracks "first meetings").

---

## Milestone and Progress Tracking

**Faith Milestone** - A significant spiritual milestone or achievement that a contact has reached in their faith journey.

**Faith Milestone Totals** - A tile showing the count of contacts at each faith milestone stage.

**Milestone** - Individual progress markers on contacts. The dashboard tracks milestones achieved by a user's contacts.

**Benchmarks** - Performance metrics comparing a user's progress over 30-day periods. Includes contacts assigned, meetings held, and milestones reached.

**Personal Benchmarks** - A tile displaying comparative charts showing metrics from the previous 30-day period vs. the current 30-day period.

**Previous Period** - In benchmarks context, refers to 60-30 days ago.

**Current Period** - In benchmarks context, refers to the last 30 days.

---

## Activity and Task Management

**Activity Log** - A record of actions and changes made in the system. The User Activity tile displays recent activity from an activity log system.

**Task** - An action item assigned to a contact or user. Tasks can have reminders, notes, and completion status.

**Task Status** - Can be "task_complete" (completed) or incomplete.

**Reminder** - A task category indicating a notification-based reminder rather than an actionable task.

**Notification Sent** - A reminder task that has had its notification delivered.

**Task Note** - Text description or details associated with a task.

---

## Database and Data Terms

**Post Meta** - WordPress post metadata. Contact fields are stored as post meta keys and values:
- `assigned_to` - Which user the contact is assigned to (format: "user-{user_id}")
- `overall_status` - Contact status (values: "assigned", "active", "closed", etc.)
- `requires_update` - Boolean indicating if contact needs follow-up
- `seeker_path` - Current seeker path stage of the contact
- `milestones` - Faith milestone achievements
- `type` - Contact type (values: "access", "user", etc.)
- `last_modified` - Timestamp of last update

**dt_activity_log** - Database table tracking activity history. Used for benchmarks and statistics queries.

**dt_post_user_meta** - Database table storing user-specific post data like tasks and reminders.

**Object Type** - The type of post being tracked (e.g., "contacts").

**User Option** - WordPress user metadata storing user-specific preferences and settings.

**Workload Status** - User metadata storing current availability status for new contacts.

---

## UI and Control Terms

**Add a Contact** - A button/action to create a new contact in the system.

**Accept/Decline** - Actions available on pending contacts to accept or decline assignment.

**Hide/Show** - Actions to hide or show specific tiles from the dashboard view.

**See All** - A link to view the complete list of items when a tile is showing a truncated view.

**Caught Up** - Message displayed when there are no more pending items (e.g., "Hurray! You are caught up.").

**Filter ID** - A parameter used in URLs to filter contacts (e.g., `filter_id=my_update_needed`).

---

## REST API Terms

**REST Endpoint** - API endpoint paths under `dt-dashboard/v1/` namespace:
- `/stats` - Get overall statistics
- `/benchmarks` - Get personal benchmarks
- `/seeker_path_personal` - Get seeker path distribution
- `/milestones` - Get milestone totals
- `/tasks` - Get user's tasks
- `/user` - Update user preferences
- `/tiles/sort` - Update tile sort order
- `/user/tiles/toggle` - Toggle tile visibility
- `/tile` - Get specific tile data

**Permission Callback** - REST API security check using `access_disciple_tools` capability.

**Namespace** - REST API namespace prefix (`dt-dashboard/v1`).

---

## Plugin Architecture Terms

**Tile Registration** - The process of registering a tile to make it available on the dashboard. Done via `DT_Dashboard_Plugin_Tiles::instance()->register()`.

**Tile Setup** - Callback function that registers assets (scripts/styles) needed by a tile.

**Tile Render** - Callback function that outputs the HTML markup for a tile.

**On Add Event** - JavaScript event fired when a tile is added/shown on the dashboard. Triggers tile's initialization JavaScript.

**On Remove Event** - JavaScript event fired when a tile is removed/hidden from the dashboard.

**Tile Context** - Object containing `wpApiDashboard` data and DOM element reference passed to tile JavaScript callbacks.

**Plugin Filter** - The `dt_dashboard_tiles` filter allowing modification of registered tiles.

**Callback Tile** - A tile using callback functions instead of a class-based implementation.

**Plugin Tile** - A tile class extending `DT_Dashboard_Plugin_Tile` with automatic file-based template and script discovery.

---

## User and Role Terms

**Disciple Maker** - A user/multiplier in the system who is actively making disciples and managing contacts.

**Multiplier** - Another term for a disciple maker with leadership responsibility.

**Dispatcher** - A user with authority to assign contacts to multipliers and monitor workload status.

**Administrator** - WordPress user role with `manage_dt` capability needed to access dashboard settings.

**Current User** - The logged-in user viewing the dashboard.

---

## Settings and Configuration

**Dashboard Layout** - The default arrangement and visibility of tiles for new users, configurable by administrators.

**User Preference** - Tile visibility and sort order preferences stored per-user.

**Nonce** - Security token for AJAX requests and form submissions.

---

## Localization Terms

**Text Domain** - `disciple-tools-dashboard` - Used for translatable strings.

**Locale** - The user's language setting determining which translations are loaded.

**Internationalization (i18n)** - System for supporting multiple languages via `__()` and `esc_html__()` functions.

---

## Chart and Visualization Terms

**AmCharts** - JavaScript charting library used for pie and column charts in benchmarks and seeker path tiles.

**Pie Chart** - Circular chart showing seeker path distribution percentages.

**Column Chart** - Bar chart comparing metrics across time periods in personal benchmarks.

**Chart Data** - Array of values and labels passed to chart visualization.

**Legend** - Key/reference showing chart categories and colors.

---

## Time and Date Terms

**30-Day Period** - Standard lookback period for benchmark calculations (current 30 days).

**60-Day Period** - Extended lookback period for comparing previous period in benchmarks (60-30 days ago).

**Last Modified** - Timestamp indicating when a contact was last updated. Dashboard displays "X days since last update".

**Date Filter** - Filtering contacts or activities by date ranges.
