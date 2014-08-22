![Build Status](https://travis-ci.org/lucifer4o/zebraenhance.svg?branch=master)

Friend list enchance
============

Zebra enhance

  Description:

    Enhances PhpBB Zebra module adding additional ACL level.
    Adds support for friend request need to be approved.
    Adds addition level of friends - hidden mark for special friends.
    Adds beautiful friend control.

  Features:

    System:
      - Make sure AJAX Callback function is loaded only in UCP -> Zebra
      - Add notifications for new requests.

    UCP:
      - Show pending and awaiting confirmation requests
      - Show beautiful friend control (using AJAX)
      - Add option for selecting "Close Friends" with additional access*
      - Dynamically locate which is the zebra module
      - Cancel request use AJAXed "confirm_box"
      - See if when user is deleted zebra cleans the remains (if not - make the extension do it)
      - Add ACL who can view friendlist

    Profile:
      - Add friend list in profile

  Installation:

    - create $phpbb_root/ext/anavaro folder
    - cd $phpbb_root/ext/anavaro
    - git clone https://github.com/lucifer4o/zebraenhance.git
    - Go to admin panel -> customize -> extensions -> Enable Friendlist Enhance

  Credits:

    @phpbb-es aka Raul [ThE KuKa]
    
  Submitting translations/functions
    
    Please fork the repo and submit every translation/patch as Pull Request.

