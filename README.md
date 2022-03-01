# Event Table Edit Old

Event Table Edit (abbrev. ETE) is a free and open source table plugin for **Joomla 3.x** (I am working on Joomla 4.x and PHP 8.0 right now). 

### Update 01.03.2022: We are proud to release ETE for Joomla 4.x here: https://github.com/Theophilix/EventTableEdit. Therefor, repo name of this repo has been changed to "old".

With the plugin, you can create a responsive, editable table with CSV import and export function and XML export/import for table settings. It also has a full rights management (Joomla ACL). A *small additional plugin (Content - Load ETE)* allows users to display two or more tables in an article (since 4.8.4 version). It is installed automatically, but can be uninstalled without any consequences if this function is not desired.
You can  transform the table into an appointment booking system with confirmation emails for users and the admin, including iCal calendar files for both in the attachment. Moreover, you can use the booking system to create a volunteer table for an event, where volunteers can enter their names if they want to help for a certain time.
As it is based on a CSS-template, the layout of the table can be changed easily. The responsive function is based on the "Column Toggle Table with Mini Map" from the tablesaw plugins (https://github.com/filamentgroup/tablesaw).

Try all the functions (including backend) on the demo site: https://demo.eventtableedit.com. Log in as user or admin with the given login credentials.



**Download latest version 4.8.8** (release date: 02.07.2021): https://github.com/Theophilix/event-table-edit/archive/master.zip.

Download older versions here: https://github.com/Theophilix/event-table-edit/releases.

Version history: scroll down to "II".

## I Features:

- Editable table (insert pictures, BBCode...)
- Sorting options (A-Z, Z-A, natural sorting is used)
- Choice of layout mode (stack, swipe, toggle) for enhanced responsiveness
- Instant filter / search
- Search and replace function (if admin is logged in)
- Multiple appointment booking function with confirmation email and ICAL calendar (.ics file) attachment, admin can edit cells (bookings) from frontend.
- Complete rights management (Joomla ACL: add/delete rows, edit cells, rearrange rows, administer table from frontend)
- Multilingual (currently available: DE, EN)
- CSV and TXT import with different formats (text, date, time, integer, float, boolean, four state, link, mail) 
  and import settings (separator, values in quotes or not)
- CSV Export
- XML import and export: import and export a table (normal or appointment) with all settings
- Own CSS based template
- A small additional plugin ("Content - Load ETE") allows users to display two or more tables in an article (since 4.8.4). It is installed automatically but can be uninstalled without any consequences if this function is not desired.

Frontend view options:
- Sort columns (setting in rights management)
- Delete rows (setting in rights management)
- Add rows (setting in rights management)
- Filter rows / Instant search
- Pagination
- Print view
- Administer table (setting in rights management) with quick csv export and import (new since 4.8.7)


Backend options:

a) General
- Normal or appointment booking function
- Options for appointment booking function:
  + ICAL / .ics-File options (location, subject, name of file)
  + Set admin email address and email display name
  + Confirmation email settings (chose subject and message text with appointment-date and -time-variables)
  + CSV Import and Export (quick csv export and import via admin login in frontend)
  + Show or hide user names to user or admin
  + Set timelimit for bookings
  + option to send two or more appoinment informations in one ics file
  + add global options, so admins can offer options (p. ex. different persons or services) and users can choose them from a list. If a user clicks on an option, the specific appointment table, that has been set in backend, is loaded.
- Show or hide table title
- Usertext before and after table
- Show or hide column to delete or sort rows
- Enable automatic column sorting when table is loaded
- Use Metadata
- Enhanced SEO
- Support for BB-Code (including emoticons and option to censor offensive words, more info: https://github.com/milesj/decoda)

b) Layout / Style

Choose or select:
- Date format
- Time format
- Float separator ("," or ".")
- Cell spacing
- Cell padding
- Colors of alternating rows
- Maximum length of cell content
- Display table in the same, or a new window
- Activate table scroll function, define height

Please post all feature requests in the issues tab.


## II Version history

**For version 4.8.8:**

[1] Normal mode
- bugfix: Frontend problems after updating to 4.8.6 https://github.com/Theophilix/event-table-edit/issues/183
- bugfix: Fehler 1054 - Unknown column 'corresptable' in 'field list' #186 https://github.com/Theophilix/event-table-edit/issues/186
- bugfix: Error '500 - JHtmlIcon::email not found.' when plugin Content - Load ETE is activated https://github.com/Theophilix/event-table-edit/issues/187

[2] Appointment mode
- bugfix: Frontend: Appointment tables: Swipe layout - Last swiping - move produces error https://github.com/Theophilix/event-table-edit/issues/185


**For version 4.8.7:**

[1] Normal mode
- enhancement: Frontend: Admin view: New buttons for csv export + import next to administrate table button with drop field https://github.com/Theophilix/event-table-edit/issues/113
- enhancement: Frontend: Appointment tables: Frontend and backend: Enable all layout modes https://github.com/Theophilix/event-table-edit/issues/180
- bugfix: Normal tables: Can not create a new table - forms are missing https://github.com/Theophilix/event-table-edit/issues/182

[2] Appointment mode
- enhancement: Frontend: Admin view: New buttons for csv export + import next to administrate table button with drop field https://github.com/Theophilix/event-table-edit/issues/113
- bugfix:  Appointment tables: Frontend: Admin can't edit and change values while logged in https://github.com/Theophilix/event-table-edit/issues/181
- bugfix: Normal tables: Can not create a new table - forms are missing https://github.com/Theophilix/event-table-edit/issues/182


**For version 4.8.6:**

[1] Normal mode
- enhancement: Frontend: Admin view: Search and replace values https://github.com/Theophilix/event-table-edit/issues/172


**For version 4.8.5:**

[x] Code improvement
- enhancement: Improve and secure ETE-code: Check all ETE .php / .js / .html files or pages with good checking software https://github.com/Theophilix/event-table-edit/issues/170

- enhancement: Code: Remove all redundant "normal table" code (filter, datatypes...) in files related to appointment booking system https://github.com/Theophilix/event-table-edit/issues/53

**For version 4.8.4:**

[1] Normal mode
- enhancement: Frontend: Update bbcode parser https://github.com/Theophilix/event-table-edit/issues/136
- enhancement: Frontend: Add multiple ETE tables to a Joomla page (perhaps with squared brackets [ETE_Tablename]?)https://github.com/Theophilix/event-table-edit/issues/160
- bugfix: Frontend: Keep chosen layout when sorting columns https://github.com/Theophilix/event-table-edit/issues/164
- bugfix: Frontend: Cursor not at the end of popup value in cells with text or date https://github.com/Theophilix/event-table-edit/issues/169
- bugfix: Backend: Layout: Improve layout https://github.com/Theophilix/event-table-edit/issues/168

**For version 4.8.3:**

[1] Normal mode
- enhancement: Backend: Style tab: show example result for date format and time format and colors on mouseover https://github.com/Theophilix/event-table-edit/issues/57
- enhancement: Frontend: Shorten long text in cells with css overflow: hidden and text-overflow: ellipsis https://github.com/Theophilix/event-table-edit/issues/137
- bugfix: Frontend: Frontend: Layout: Layout problems with checkbox list in column toggle mode https://github.com/Theophilix/event-table-edit/issues/163
- bugfix: Frontend: Dropdowns don't work https://github.com/Theophilix/event-table-edit/issues/166
- bugfix: Frontend: UX: Put the typing focus on popup, so user can type directly https://github.com/Theophilix/event-table-edit/issues/167


**For version 4.8.2:**

[1] Normal mode

- bugfix: Backend: CSV Import: English language instead of german https://github.com/Theophilix/event-table-edit/issues/148
- bugfix: Frontend: Normal table: Layout broken when adding new line in stack or swipe mode https://github.com/Theophilix/event-table-edit/issues/159
- bugfix: Frontend: Normal table: edit sort order column disappears if table is sorted by another column https://github.com/Theophilix/event-table-edit/issues/157


[2] Appointment mode

- bugfix: Backend: Appointment table: Fix incorrect layout of all mouse-over tool tips https://github.com/Theophilix/event-table-edit/issues/154
- bugfix: Frontend: Appointment table: layout problems in booking window https://github.com/Theophilix/event-table-edit/issues/155


**For version 4.8.1:**

[1] Normal mode
- bugfix: Backend: Navi-Menu: No tooltips in XML / CSV Import menus https://github.com/Theophilix/event-table-edit/issues/153
- bugfix: Frontend: Layout: Always hide deleting/sorting column if deleting/sorting is forbidden https://github.com/Theophilix/event-table-edit/issues/145
- bugfix: Backend: Check all popups, remove obsolete mouseover popups https://github.com/Theophilix/event-table-edit/issues/72
- bugfix: Backend: ACL setting access level in backend not working https://github.com/Theophilix/event-table-edit/issues/161
- enhancement: Update ETE to newest table saw version https://github.com/Theophilix/event-table-edit/issues/93


**For version 4.8:**

[1] Normal mode

- bugfix: Backend: Layout: Info that is shown on mouseover differs in layout https://github.com/Theophilix/event-table-edit/issues/150

- bugfix: Frontend: Layout: Fix layout problem with stack view https://github.com/Theophilix/event-table-edit/issues/143

- bugfix: Frontend: Pagination shown if a page class is set in the menu that links to the table https://github.com/Theophilix/event-table-edit/issues/134

- bugfix: Frontend: Layout: Center Sorting input box vertically  https://github.com/Theophilix/event-table-edit/issues/116

- bugfix: Frontend: Layout: Improve layout (buttons, fields etc.) https://github.com/Theophilix/event-table-edit/issues/111


**For version 4.7.9:**

[1] Normal mode

- bugfix: Frontend: Frontend: No calendar picker when editing date cells https://github.com/Theophilix/event-table-edit/issues/152


**For version 4.7.8:**

[1] Normal mode

- enhancement: Frontend: Join both filters into one instant filter again https://github.com/Theophilix/event-table-edit/issues/151


**For version 4.7.7:**

[1] Normal mode

- enhancement: Frontend: Layout: Change order of layout modes in list https://github.com/Theophilix/event-table-edit/issues/144
- enhancement: Backend: Layout option: Choose standard layout (choose from swipe, stack, toggle) https://github.com/Theophilix/event-table-edit/issues/149 


**For version 4.7.6:**

[1] Normal mode

- bugfix: Frontend: Sorting with multiple pages https://github.com/Theophilix/event-table-edit/issues/21

[2] Appointment mode

- enhancement: Frontend + Backend / Appointment tables: Add dropdown list with global options https://github.com/Theophilix/event-table-edit/issues/133
- bugfix: Frontend: Appointment function: Sort order column is visible if admin logs in https://github.com/Theophilix/event-table-edit/issues/142

[3] Other

- Frontend/Backend: Fix language issues for german language https://github.com/Theophilix/event-table-edit/issues/141

**For version 4.7.5:**

[1] Normal mode

- bugfix: Frontend: Keep showing first row when scrolling down in long tables https://github.com/Theophilix/event-table-edit/issues/66


**For version 4.7.4:**

[1] Normal mode

- bugfix: Frontend: Layout: Layout not looking good at 1280+ pixel width if delete column is activated: https://github.com/Theophilix/event-table-edit/issues/123
- activate table scroll function, define height https://github.com/Theophilix/event-table-edit/issues/66 (not working well, do not use this option!)

[2] Appointment mode

- enhancement: Appointment tables: Sort ICS files / date info in email by date:  https://github.com/Theophilix/event-table-edit/issues/125
- enhancement: Frontend / Appointment tables: Show same date information in email as after click of booking button: https://github.com/Theophilix/event-table-edit/issues/131
- enhancement: Frontend / Appointment tables: option to send two or more appoinment informations in one ics file: https://github.com/Theophilix/event-table-edit/issues/132


**For version 4.7.3:**

[1] Normal Mode
- enhancement: Frontend / Backend: Enable sorting when the browser loads the table: https://github.com/Theophilix/event-table-edit/issues/128
- bugfix: Hide the delete icon for unauthorized users: https://github.com/Theophilix/event-table-edit/issues/129


**For version 4.7.2:**

[1] Normal Mode
- bugfix: Frontend: Search function not working with email datatype cells https://github.com/Theophilix/event-table-edit/issues/122
- bugfix: Update from Version 4.5.4 or lower to actual versions: missing timestamp column causes error https://github.com/Theophilix/event-table-edit/issues/130


**For version 4.7.1:**

[0] General:
- bugfix: differing page titles: https://github.com/Theophilix/event-table-edit/issues/121

[2]Appointment mode
- bugfix: Appointment tables: Bug with time / leading zero: https://github.com/Theophilix/event-table-edit/issues/120


**For version 4.7:**

[1] Normal Mode
- bugfix: Frontend: Date displayed in wrong format / accept different date formats and enable search: https://github.com/Theophilix/event-table-edit/issues/118

- bugfix: Frontend: Deleting column is still clickable: https://github.com/Theophilix/event-table-edit/issues/117

- bugfix: Frontend: Layout: Layout problem when sorting and deleting columns are both active:
https://github.com/Theophilix/event-table-edit/issues/115

- bugfix: Frontend: Layout: Keep view (toggle, swipe/stack) also when searching a date or letters/numbers
https://github.com/Theophilix/event-table-edit/issues/112

- enhancement: Backend: CSV-Import: Remove datatype choice for appointment tables, remove menu.
https://github.com/Theophilix/event-table-edit/issues/110

- bugfix: Frontend: Layout: Normal mode: If user clicks on date, only actual date is shown.:
https://github.com/Theophilix/event-table-edit/issues/109

- bugfix: Frontend: Layout: Swipe mode: last swiping action greys out the button, no return possible:
https://github.com/Theophilix/event-table-edit/issues/107

- bugfix: Frontend: Layout: problems with stack mode: 
https://github.com/Theophilix/event-table-edit/issues/104

- bugfix: Frontend: Layout: Stack mode: Popup is transparent, sorting and layout mode is hidden / overlapped.:
https://github.com/Theophilix/event-table-edit/issues/105

- enhancement: backend: general: add new column "last changes" for normal table overwiew
https://github.com/Theophilix/event-table-edit/issues/97

- enhancement: Frontend: Enable stack view also for large screens:
https://github.com/Theophilix/event-table-edit/issues/52

[2] Appointment mode

- enhancement: Backend: Appointment: Fill newly created appointment table with data
https://github.com/Theophilix/event-table-edit/issues/85



**For version 4.6.6:**

[0 General]

- enhancement: Backend: CSV Import: Go to menu type of imported table: https://github.com/Theophilix/event-table-edit/issues/103

- bugfix: Backend: CSV Export: When leaving out table name an error appears https://github.com/Theophilix/event-table-edit/issues/100

- bugfix: Backend: xml import: wrong german spelling "warnung" should be "Warnung" (capital letter): https://github.com/Theophilix/event-table-edit/issues/99

- bugfix: Backend: Appointment tables overview: options not working, are linking back to normal tables menu
https://github.com/Theophilix/event-table-edit/issues/98

[1] Normal mode

- enhancement: Frontend: Sorting options: Change "Timestamp" (+ arrow up/down) to "Newest"/"Oldest" https://github.com/Theophilix/event-table-edit/issues/101

- bugfix: Frontend: Column Toggle Mode: When hiding a column, each click on a cell refreshes tablehttps://github.com/Theophilix/event-table-edit/issues/102


**For version 4.6.5:**
[0] General
- enhancement: backend: csv/xml export - activate function to export and import hidden timestamp column https://github.com/Theophilix/event-table-edit/issues/95
- enhancement: Backend: when importing a xml, go to the menu type of imported table https://github.com/Theophilix/event-table-edit/issues/86
- enhancement: Backend: Show ETE version number https://github.com/Theophilix/event-table-edit/issues/83
- bug: backend: general: name of menu is in wrong language https://github.com/Theophilix/event-table-edit/issues/96

[1] Normal mode
- Frontend: Layout: Center the pagination select box vertically https://github.com/Theophilix/event-table-edit/issues/82


[2] Appointment mode
- enhancement: Appointment / layout: Make "blocked (time limit)" cell as small as reserved cell and remove line spacing between words https://github.com/Theophilix/event-table-edit/issues/56



**For version 4.6.4:**

[0] General

- bugfix: backend: XML Export: don't save any files on webspace https://github.com/Theophilix/event-table-edit/issues/84
- bugfix: backend: csv import: imported csv in wrong category (normal instead of appointment), even if settings are correct https://github.com/Theophilix/event-table-edit/issues/90
- bugfix: backend: xml import: Warning when trying to upload wrong file format is green https://github.com/Theophilix/event-table-edit/issues/94

[1] Normal mode

- bugfix: 
backend: remove notice when admin edits name (appointment mode) oder edits cell (normal mode) https://github.com/Theophilix/event-table-edit/issues/92


[2] Appointment mode

- enhancement: backend: appointment table: add variable first_name and last_name to summary field in ics-file https://github.com/Theophilix/event-table-edit/issues/91




**For version 4.6.3:**

[General]

- enhancement: Backend: Consequent separation of appointment and normal function tables https://github.com/Theophilix/event-table-edit/issues/59 (Delete column is working now!)

- enhancement: Frontend / Layout: give delete buttons it's own column, separate it from the change sort order column https://github.com/Theophilix/event-table-edit/issues/49

**For version 4.6.2:**

[General]

- bugfix: frontend: In swipe mode, the pagination module is hidden when clicking on swipe buttons https://github.com/Theophilix/event-table-edit/issues/51
- bugfix: frontend: Automatically choose stack view for mobile screens https://github.com/Theophilix/event-table-edit/issues/61
- enhancement: backend: Consequent separation of appointment and normal function tables https://github.com/Theophilix/event-table-edit/issues/59 (Delete column is not working yet!)

[1] Normal mode
- bugfix: Notices: "undefined offset" and " Trying to get property of non-object" https://github.com/Theophilix/event-table-edit/issues/80
- bugfix:  Caching interferes with edit rights https://github.com/Theophilix/event-table-edit/issues/79
<<<<<<< HEAD
=======
- bugfix: frontend: Administer table save buttons leads to loss of data https://github.com/Theophilix/event-table-edit/issues/88
>>>>>>> origin/master

**For version 4.6.1:**

[1] Normal mode
- bugfix: notices: "undefined offset" and " Trying to get property of non-object" https://github.com/Theophilix/event-table-edit/issues/80
- bugfix: Caching interferes with edit rights https://github.com/Theophilix/event-table-edit/issues/79

**For version 4.6:**

[1] Normal mode
- enhancement: New four state datatype https://github.com/Theophilix/event-table-edit/issues/33
- bugfix: stack sorting problem: https://github.com/Theophilix/event-table-edit/issues/77
- bugfix: changing ACL settings takes a long time to be saved / made https://github.com/Theophilix/event-table-edit/issues/73


**For version 4.5.5:**

[1] Normal mode
- enhancement: sorting by timestamp https://github.com/Theophilix/event-table-edit/issues/74
- bugfix: changed pagination rows standard from 15 to 100: If appointment system is in use, 15 rows are too few. If there are more than 15 rows, the wrong time and date was in ics-File https://github.com/Theophilix/event-table-edit/issues/76.

**For version 4.5.4:**

[1] Normal mode
- bugfix: calendar not working https://github.com/Theophilix/event-table-edit/issues/64
- bugfix: compatibility problems with PHP-Version 7.0 and 7.1 https://github.com/Theophilix/event-table-edit/issues/65


**For version 4.5.3:**

[2] Appointment mode
- enhancement: https://github.com/Theophilix/event-table-edit/issues/42: Frontend / Appointment function: Allow multiple bookings for the same day and time
- enhancement: new options in backend: "show username to admin" and  "show username to user".
- enhancement: admin can insert several usernames separated by ENTER key.
- bugfix: print view now shows exactly what user sees

[3] Universal changes
- bugfix / enhancement: xml download: proposed filename is table name


**For version 4.5.2:**

[1] Normal mode
- bugfix: layout mode change when using pagination https://github.com/Theophilix/event-table-edit/issues/30

[3] Universal changes
- bugfix / enhancement: xml import error and version handling

**For version 4.5.1:**

[1] Normal mode
- bugfix: uploaded wrong stringparser file -> bbcode works now
- bugfix: “deprecated” warnings in development debug mode

[2] Appointment mode
- enhancement: https://github.com/Theophilix/event-table-edit/issues/41: Frontend / Appointment table: admins can edit table values directly (change free -> reserved and vice versa) when logged in to frontend.


**For version 4.5:**

[1] Normal mode
- enhancement: natural sorting, not perfect yet, but work on it will continue
- bugfix: BBCode is working now
- bugfix: php 7.1 modulo by zero
- bugfix: 800px width (deleting column disappeared)
- bugfix: sort date problem when deleting content of date cell
- bugfix: dropdown fields error
- bugfix: filter problem:  a) Umlaut ä/ö/ü + b) filter not working with enter key + c) value does not stay in filter input form after clicking on “show” -> now, 2 input fields
- bugfix: firefox browser asks to refresh page after editing cells

[2] Appointment mode
- bugfix: admin doesn’t get email (was problem with „/“) + email does not show multiple appointments (example:  you have an appointment on 05.03.2019 / 06.03.2019 at 17:20 / 17:40)


**For version 4.4.3:**

[1] Normal mode
- bug removed: https://github.com/Theophilix/event-table-edit/issues/27: Locale not recognized (in date format) (?)
- bug removed: https://github.com/Theophilix/event-table-edit/issues/39: When adding a new row, refreshing page is necessary before editing cell.
- Joomla update notification and update via Joomla administrator backend enabled. 


**For version 4.4.2:**

[1] Normal mode
- bug removed: https://github.com/Theophilix/event-table-edit/issues/31: Sorting row column disappears in toggle view

**For version 4.4.1:**

[1] Normal mode
- bug removed: https://github.com/Theophilix/event-table-edit/issues/28: Sorting by date field results in an error if date field is empty

[3] Universal changes
- english corrections: https://github.com/Theophilix/event-table-edit/pull/29 -> Thank you, user "brianteeman"!

**For version 4.4:**

[1] Normal mode
- bug removed: when importing empty cells from csv-file, cell value changed to "free". Solution: select box "csv is appointment-table".

[2] Appointment mode
- bug removed: ICS files are deleted now

[3] Universal changes
- minor spellings errors corrected
- xml import/export: now xml-files are scanned for ETE-signature
- plugin update notification and update function via Joomla backend reactivated

**For version 4.3:**

[1] Normal mode
- XML import and export function: export and import a table with all settings.
- file download option for csv export
- additional delete and sort row function renamed. It is called "Additional row sorting and deleting column"(ger:"Zusatzspalte zur Umsortierung und Löschung der Tabellenzeilen")

[2] Appointment mode
- no changes

**For version 4.2:**

[1] Normal mode
- Column sorting (via header click or selection menu) option added (unfortunately, natural sorting is still not working)
- Table administrate view bug is fixed
- Choice of layout mode (stack, swipe, toggle)-option added
- Backend overview improved

[2] Appointment mode
- Selection of multiple appointments added. Users have to click a button after selecting dates/times.
- Time limit option (cells are marked as "blocked") added
- "Add weekdays to header" option added
- Layout improvements in frontend and backend
