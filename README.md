# RiiConnect24 Mail Scripts
These are the production-grade scripts we use at RiiConnect24.

Schema is available in the `mysql` folder.

Scripts are available in the `php` folder.

Pruning scripts are in `clean` folder. Run these every so often.
# Setup
1. Patch your nwc24msg.cfg - RC24 Bot contains code to do this. Just change the URLs accordingly.
2. Use RiiConnect24's very vulnerable IOS patch that allows any server to work because we seem to not understand how to do `res` properly and nobody wanted to work on it after we released. Oh well.
3. Import the schema, and run the scripts on your server - see below for replacing.

# Replacements

## send.php
- `SENDGRIDAPIKEY` - Self-explanatory
- `YOURDOMAIN` - the domain you patch in nwc24msg.cfg (for validation)
## mysql.php
- `USER`, `PASS`, `DATABASE` - MySQL login details.

# Credits
- thejsa for MySQL code and like all of send.php's working send code.
- spotlight_is_ok for pushing for OSS and making me see reason.
- Larsenv for being whiny as usual, and whining when it didn't work.
- PokeAcer for losing his morality and his depressive bouts making him see reason.

# Help Out!

Want to help out? ~~You're stupid~~
- The Mail system has _no_ security, we need security.
- Automatic patcher for server(s).
- Interconnectivity between servers so that we don't fracture the community.
