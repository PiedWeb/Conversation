<p align="center"><a href="https://dev.piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="conversation static website" />
</a></p>

# Conversation

[![Latest Version](https://img.shields.io/github/tag/piedweb/conversation.svg?style=flat&label=release)](https://github.com/PiedWeb/conversation/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/conversation/master.svg?style=flat)](https://travis-ci.org/PiedWeb/conversation)
[![Quality Score](https://img.shields.io/scrutinizer/g/piedweb/conversation.svg?style=flat)](https://scrutinizer-ci.com/g/piedweb/conversation)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/conversation.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/conversation/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/conversation.svg?style=flat)](https://packagist.org/packages/piedweb/conversation)

Extending a page with **comments**, a **contact** form or just an **user input**...

**Add conversation on a static website.**

Initially dev to be use with [PiedWeb/CMS](https://github.com/PiedWeb/CMS).

## Installation

Via [Packagist](https://packagist.org/packages/piedweb/conversation) :

```
# Get the Bundle
composer require piedweb/conversation

# Add the route to your Routes:
conversation:
    resource: '@PiedWebConversationBundle/Resources/config/routes/conversation.yaml'
```

Update sonata_admin config file to add an navlink :
```
        groups:
            app.admin.group.page:
                label: admin.label.content
                label_catalogue: messages
                items:
                    - piedweb.admin.page
                    - piedweb.admin.media
                    - piedweb.admin.conversation
```
(or `ln -s -f vendor/piedweb/cms-bundle/src/Resources/config/packages/sonata_admin.fullFeatured.yaml config/packages/sonata_admin.yaml`)

## Usage

### You can use it as is and include it in your Page with two manners :

```bash
# Load form via fetch (javascript)
<div data-live="{{ path('piedweb_cms_conversation', {'type': 'newsletter', 'referring': 'nslttr-'~page.slug}) }}"></div>

# Render form in Controller
{{ render(controller('PiedWeb\\ConversationBundle\\Controller\\ConversationFormController::show')) }}
```

### Render published comment

```twig
{{ listMessage(referring[, orderBy, limit, template]) }}
```

### Get mail notification for new message

Configure the bundle (`piedweb_conversation.notification_emailTo`) and programm a cron :

```
bin/console conversation:notify
```

## Customization

## Small rendering customization

By overriding `@PiedWebConversation/_conversation.html.twig`
(or `'@PiedWebConversation/_'.$type.'Step'.$step.'.html.twig`
or `'@PiedWebConversation/_'.$type.$referring.'Step'.$step.'.html.twig`).

## Create a new form

Per default, there is 3 form types : `newsletter`, `message` and `multiStepMessage`.

Add a new class in config `piedweb_conversation.form.myNewType: myNewFormClass`.

## TODO

- [ ] Test
- [ ] Remove bootstrap class from default view files (by moving them to PiedWebThemeComponent)
- [ ] Email validator for new message

## Contributors

- [Robin](https://www.robin-d.fr/) / [Pied Web](https://piedweb.com)
- ...

## License

MIT (see the LICENSE file for details)
