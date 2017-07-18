# New ADR Design document

## Requirements

- scaleable
- extendable

## limits

- Must assign base leaders
- multi slot per position?

### Billet

- Leader(?) - multi?
- Sub-units (assign leader(?))
- can assign people to same unit, give title/perms (basic/admin)

#### SQL tables

##### Team

- [ ] id - int(10)
- [ ] Name - VARCHAR(255)
- [ ] Remark - smallText
- [ ] managed_billets (serialised list of billet id's)
- [ ] users (serialised list of user ids)
- [ ] can_extend - int (bool)
- [ ] order - int(10)

##### Team user

- [ ] id - int(10)
- [ ] user_id - int(10)
- [ ] title
- [ ] admin - int (bool)
- [ ] mods - int (bool)
- [ ] order - int(10)

### Abilities

**Billet admin can:**

- Create new Billet in group
- Manage all subsequent Billets
- Assign users to Billets

**Billet Mod can:**

- Manage current billet (other than admins)
- Assign users to current billet

```
hq
    support
        department
            team
            team
            ...
        department
            ...
        ...
    primary
        company
            platoon
                squad
                squad
                ...
            platoon
                ...
            ...
        company
            ...
        ...
```
