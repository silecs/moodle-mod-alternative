# Module Alternative: teacher documentation

## Overview

The *Alternative* activity allow students to register to one or several choices,
among a given list.

It can fulfill several purposes: registration for a conference, choice of
a date for an examination, of an internship, etc.

Alternative is similar to the official "Choice" module, but has many settings
to adapt to various needs:

- individual or team registration (with a team leader)
- possible quota on each option
- single choice, or limited/unlimited number of choices
- manual input for the options, or import of a csv file
- statistics
- csv export of the registrations
- group enrolment according to user choices


## Details

### Teacher settings

Alternative is a normal activity.
It is inserted into a course in the same way as other activities.

The settings of an Alternative begins with fields like any other activity
(name, description, etc).

Then, the specific settings are:
allowing modification of a choice, compact/detailed display, team mode, multiple choices.
Each setting has a detailed desciption in the Moodle help (button "?").

The settings end with the input for the different options.
This can use a web form for manual input, or a CSV file import.

For the manual input, there are 2 fields, and a button allows the teacher to add
2 more, as many times as necessary.

#### Conditional access
To set a restricted time period for this activity, the site administrator
must enable the "_enableavailability_" setting on the page
"_Site administration » Advanced features_".


### Teacher administration

Once the activity is created, four or five new entries are available in the
menu _Settings » Alternative administration_, leading to the following views:

- View synthesis
- View registrations
- View teams (if enabled)
- View registered users
- View unregistered users


#### View synthesis
The synthesis page displays the global statistics in a table.
On the last two lines, the figures are active links towards the matching views.

From this view, one link and two buttons allow the teacher to:

- fetch a csv export of the registrations,
- force the student registrations,
- send a reminder message to students without any registration.

#### View registrations
This table displays the registrations, one option by row.
All the sudents that registered into the same option are listed in the same row.

#### View teams
This table displays the registrations, one team by row.
Each row displays the team, the size, the chosen options, and highlights the team leader.

#### View registered users
This table displays the registrations, one student by row.
If multiple choices are allowed, all the chosen options are listed in the same row.

#### View unregistered users
This table displays the unregistered users.
For each of them, a button enables the teacher to force the registration.


### Student registration

In the (default) *individual mode*, a student that opens the activity can
simply register to one or several options.
If allowed, he can later modify its registrations.

In the *team mode*, he can register himself and several other team members.
In this case, he becomes the team leader.
