# Module Alternative : teacher documentation

## Overview

The *Alternative* activity allow students to register to one or several choices,
among a given list.

It can be applied to several cases : registration for a conference, choice of
a date for an examination, of an internship, etc.

Alternative is similar to the official "Choice" module, but has many settings
to adapt to various needs:

- individual or team registration (with a team leader)
- possible quota on each option
- single choice, or limited, or unlimited
- manual input for the options, or importation of a csv file
- statistics
- csv export for the registrations


## Details

### Teacher settings

Alternative is a normal activity. It can be added to a course
in the same way as all the others.

Except for common fields, the specific settings are :
allowing modification of a choice, compact/detailed display, team mode, multiple choices.
Each setting is detailed in the Moodle help (button "?").

Then, there is the input for the different options, which can be a manual input,
or a CSV file import.

For the manual input, there are 2 fields, and a button allows the teacher to add
2 more, as many times as necessary.

#### Conditional access
To have a restricted time period to an activity, the site administrator
must enable the "_enableavailability_" setting on the page
"_Site administration » Advanced features_".


### Teacher administration

Once the activity is created, four or five new entries are available in the
menu _Settings » Alternative administration_ for the following views:

- View synthesis
- View registrations
- View teams (if enabled)
- View registered users
- View unregistered users


#### View synthesis
The synthesis displays the global statistics in a table.
On the last two lines, the figures are active links towards the matching views.

From this view, one link and two buttons allow the teacher to:

- fetch a csv export of the registrations,
- force the student registrations,
- send a reminder message to students without any registration.

#### View registrations
This table displays the registrations, one option by row.
All the sudents registered to the same option are listed in the same row.

#### View teams
This table displays the registrations, one team by row.
Each row displays the team, the size, the chosen options, and highlights the team leader.

#### View registered users
This table displays the registrations, one student by row.
If multiple choices are allowed, all the chosen options are listed in the same row.

#### View unregistered users
This table displays the unregistered users.
For each of them, a button allow the teacher to force the registration.


### Student registration

In the (default) *individual mode*, when a student open the activity, it can
simply register (or modify its registrations, if allowed) to one or several options.

In the *team mode*, he can register himself and several other team members.
In this case, he becomes the team leader, for later display.
