<p align="center"><a href="https://dev.piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="conversation static website" />
</a></p>

# Conversation

[![Latest Version](https://img.shields.io/github/tag/piedweb/conversation.svg?style=flat&label=release)](https://github.com/PiedWeb/conversation/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/conversation/master.svg?style=flat)](https://travis-ci.org/PiedWeb/conversation)
[![Quality Score](https://img.shields.io/scrutinizer/g/piedweb/conversation.svg?style=flat)](https://scrutinizer-ci.com/g/piedweb/conversation)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/conversation.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/conversation/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/conversation-bundle.svg?style=flat)](https://packagist.org/packages/piedweb/conversation-bundle)

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

## Usage

### You can use it as is and include it in your Page with two manners :

```bash
# Load form via fetch (javascript)
<div data-live="{{ path('piedweb_cms_conversation') }}"></div>

# Render form in Controller
{{ render(controller('PiedWeb\\ConversationBundle\\Controller\\ConversationController::show')) }}
```

## Customization

## Small rendering customization

By overriding `@PiedWebConversation/_conversation.html.twig`
(or `'@PiedWebConversation/_'.$type.'Step'.$step.'.html.twig`
or `'@PiedWebConversation/_'.$type.$referring.'Step'.$step.'.html.twig`).

## Create a new form

// todo : add a configuration array wich map `type` to `class` and load it in the ConversationController ()

For now, you have 3 form types : `newsletter`, `message` and `multiStepMessage`.

## TODO

- [ ] Test
- [ ] Remove bootstrap class from default view files (by moving them to PiedWebThemeComponent)
- [ ] Add command to program an email notifier via cron

## Contributors

- [Robin](https://www.robin-d.fr/) / [Pied Web](https://piedweb.com)
- ...

## License

MIT (see the LICENSE file for details)
