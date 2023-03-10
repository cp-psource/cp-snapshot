=== CP Snapshot ===
Contributors: DerN3rd (WMS N@W)
Donate link: https://n3rds.work/spendenaktionen/unterstuetze-unsere-psource-free-werke/
Tags: multisite, snapshot, backups, classicpress-plugin
Requires at least: 4.9
Tested up to: 5.6
Stable tag: 3.1.5
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dieses Plugin ermöglicht es Dir, bei Bedarf schnelle Backup-Snapshots Deiner funktionierenden ClassicPress-Datenbank zu erstellen.

This plugin allows you to create quick backup snapshots of your working ClassicPress database when needed.

== Description ==

## 1.1 Dashboard

The Snapshot Pro Dashboard provides an overview of your scheduled backups, what third-party destinations you’re linked to, and when your last backup was taken.

**Last Snapshot** – Lists the date and time of your most recent successful backup
**Available Destinations** – Is the number of locations your backups can be saved to. A full explanation of what destinations are and how to configure them can be found under the Destinations section of this guide.
**Backups Schedule** – Shows what time your backups are scheduled to take place each day.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-Dashboard-overview.png)

## 1.2 Snapshots

Snapshots is where backups are configured and executed, on demand or at scheduled intervals, to include all or a custom selection of files and database tables.

Click the **Create Snapshot** button to open the Snapshot Wizard.
![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-Create-Snapsot.png)

### Requirements Check

Snapshots cannot be created for a site that has not passed the Requirements Check, which verifies that the required apps and settings are present and up-to-date, and which provides recommended actions if the minimum requirements are not met:

**PHP Version** – Snapshot Pro requires PHP version 5.5 or later. If your host is using an older version of PHP Snapshot Pro will display a low PHP version or PHP version is out of date warning. You’ll need to update your PHP version to proceed.
**Max Execution Time** – A minimum execution time of 150 seconds is recommended to give the backup process the best chance of succeeding. If you use a managed host, contact them directly to have it updated.
**MySQLi** – Snapshot needs the MySQLi module to be installed and enabled on the target server. If you use a managed host, contact them directly to have this module installed and enabled.
**PHP Zip** – To unpack the zip file created by Snapshot, the PHP Zip module will need to be installed and enabled. If you use a managed host, contact them directly to have it added or updated.
If your site fails the check, correct the deficiencies, then click Re-check. Once the Requirements Check is passed, proceed with configuring a Snapshot.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-wizard-Image-2.png)


### Configuration

#### Destination

A destination is a location where Snapshot backups are stored, and come in two forms: local and remote (third-party). Snapshot creates a default local destination when the plugin is activated, but remote destinations must be connected to Snapshot before backups can be stored there.

Connected destinations appear as options within the snapshot configuration screen. Multiple destinations can be connected, but only one destination can be used for any given snapshot.

#### Local Snapshot

The default local destination created by Snapshot is a directory on the same server that houses your site. See our guidance in the Destinations section below for details on using and modifying this default local destination.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-image-3.png)

We do not recommend using your local server as your sole backup location, for security reasons discussed in the Destination section of this guide. Instead, we recommend connecting at least one remote destination and using it as your primary backup destination.

See this guide’s Destinations section below if you wish to change where your local backups are stored or need to connect a remote destination before proceeding.

#### Directory

This field is optional and can be used to change the folder in which your backup will be stored or to add dynamic values for customizing the bucket/directory, site domain, or ID.

The default local directory is set to public_html/wp-content/uploads/snapshots/, but this can be modified during configuration to store a backup elsewhere on your local server by selecting Use Custom Directory and entering the preferred directory in the field provided.

#### Files

This module allows you to choose what files Snapshot Pro includes in your backup. These options apply to files only, and not the database. Similar options for database tables are located in the Database section.

**Don’t include any files** – This option will only backup the database and will exclude all theme, plugin, and media files.
**Include common files** – Includes all themes, plugins, media files in the wp-content and uploads folders.
**Only included selected files** – Select this option to reveal a list from which you can choose what files should be included in your backup.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-image-4.png)

#### Dropbox-only Option

By default, all Snapshots are created using the traditional Archive option, which creates a single zip version of your website including all the files and database tables. However, if Dropbox is the destination, the Mirror/Sync option will replicate the site’s file structure so it can be viewed in Dropbox. Only the database will be zipped. It is important to know that Mirror/Sync backups cannot be restored using Snapshot’s one-click restore feature, but must be restored manually.

#### URL Exclusions

Every file in the WordPress directory has a unique URL. You can exclude any individual file from your backup by adding the URL to the field provided. URLs must be added one per line.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapchat-image-5.png)

#### Database

This module lets you select which database tables to include in your backup. The Include all and Don’t include any options are self-explanatory.

To select a custom array of tables to backup, select **Only include selected database tables**, then select the tables you want backed up from the menu that appears.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapchat-database-table-menu-1.png)

#### Frequency

By default, Snapshot is set to Once-off, which simply is a one-time, on demand backup. Select **Run daily, weekly or monthly** if you want to schedule automatic backups that occur on a regular basis.

Use the drop-down menus to choose the frequency (daily, weekly, monthly), the day of the week, and the time of day you want the backup to occur.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-image-6-1.png)

**Remote Storage Limit**

When scheduling recurring backups destined for remote destinations you have the option to keep all the backups Snapshot creates or limit them to a specific number. Once the limit is met, Snapshot will begin overwriting the oldest stored copy with the new backup.

Select Keep all snapshots to retain every backup Snapshot creates. Select Keep a certain number of snapshots and remove the oldest and then set the desired number of backups in the field provided.

![](https://n3rds.work/wp-content/uploads/2023/03/Snapshot-remote-storage-limit.png)

**Local Storage Limit**

You have the option to keep all the backups Snapshot stores in the cloud or limit them to a specific number. Once the limit is met, Snapshot will replace the oldest stored copy with the new copy.

Keep in mind that 10gb of cloud storage is allocated for Snapshot backups for every member account. Limiting the number of backups stored in the cloud will help minimize the rate at which those 10gb are used.

Select Keep all snapshots to retain every local backup Snapshot creates, or select Keep a certain number of snapshots and remove the oldest and then set the desired number of backups in the field provided.

Enable Also run a backup now to execute an immediate backup or disable the feature to delay the backup until it’s scheduled time.

![](https://wpmudev.com/wp-content/uploads/2020/01/local-stoarge-plus-also-run.png)

#### Name, Save, Run

Enter a name for the current Snapshot into the field provided, keeping in mind that Snapshot automatically adds a date and ID to that name. If you are satisfied with the current configuration, click Save & Run Backup to execute the Snapshot.

The time it takes to complete the backup ranges from a few minutes to a few hours depending on the size of the site.

![](https://wpmudev.com/wp-content/uploads/2020/01/Create-Snapshot-in-progress.png)

When the backup is complete, a zip file will be uploaded to the chosen destination.

#### Available Snapshots

After you’ve created your first Snapshot, a new module — Available Snapshots — will appear in the Snapshots tab and display a list of snapshots created for the configured site, along with other key data.

Use the filter located at the top of the panel to sort snapshots by destination.

![](https://wpmudev.com/wp-content/uploads/2020/01/Avalable-snapshots-module.png)

##### Options Menu

The three dots to the right of a backup opens the Options Menu. The options menu includes:

**Edit** – Click to access and modify a snapshot’s configuration.
**Regenerate** – Click to overwrite the last backup created with a fresh copy.
**Restore** – Click to open the Restore Wizard and begin restoring a site from backup, as discussed in the Restoring a Backup section below.
**Delete** – Click to delete a backup.

Use bulk actions to delete multiple Snapshots at the same time.

## 1.3 Destinations

### Understanding Destinations

A destination is a location where Snapshot backups are stored, and come in two forms: local and remote (third-party).

### Local Destination

The default destination is Local. Local backups are stored on the same server as your live site, and are great for quickly rolling back changes, which is useful during development. Local backups are not recommended for recovering after being hacked. Because the server that houses your site has been compromised, every backup on that server could be infected.

### Configure

Local backups are stored on your server here: public_html/wp-content/uploads/snapshots/.

![](https://wpmudev.com/wp-content/uploads/2020/01/Local-directory.png)

You can change the directory where your snapshot backups are stored by clicking **Configure** in the local module.

![](https://wpmudev.com/wp-content/uploads/2020/01/configure-local.png)

The name of the local directory, Local Snapshot, cannot be modified, but the directory path can be changed to any folder on your server by entering the path into the field provided and clicking Save Destination.

![](https://wpmudev.com/wp-content/uploads/2020/01/Change-local-directory.png)

### Remote Destinations

Snapshot Pro supports four remote storage destinations which can be connected using the instructions in this section: Dropbox, Google Drive, AmazonS3, and FTP/SFTP.

![](https://wpmudev.com/wp-content/uploads/2020/01/default-remotes.png)