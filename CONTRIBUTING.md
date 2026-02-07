# Contributing

Want to help? Great! Here's how.

## Before you start

- Fork the repository
- Clone your fork locally
- Set up the dev environment (see [DEVELOPMENT.md](./DEVELOPMENT.md))
- Make sure tests pass: `make test`

## Making changes

### Branch naming
```
feature/description        # New features
fix/description           # Bug fixes
docs/description         # Documentation
refactor/description     # Code cleanup
```

Example: `git checkout -b feature/add-user-validation`

### Write tests first
For every feature or fix:
1. Write a failing test
2. Implement the feature
3. Test passes
4. No test = no merge

Run tests before commit:
```bash
docker exec Rentella_app php artisan test tests/Feature/
```

### Commit messages
Use conventional commits. Clear and specific:

✅ Good:
- `feat: add ownership check to beach pictures`
- `fix: correct umbrella inventory count`
- `docs: update README with setup steps`

❌ Bad:
- `update stuff`
- `bug fix`
- `changes`

Template:
```
type: short description

Optional longer explanation if needed.
Can be multiple lines.

Fixes #123  (if closing an issue)
```

### Code style

#### Backend (Laravel)
- PSR-12 standard
- Use early returns
- Type hints on all methods
- No unused imports
- Meaningful variable names

Example:
```php
public function store(StoreBeachRequest $request): JsonResponse
{
    $owner = auth()->user();
    
    $beach = Beach::create([
        'name' => $request->validated('name'),
        'owner_id' => $owner->id,
    ]);
    
    return response()->json($beach, 201);
}
```

#### Frontend (Vue 3)
- TypeScript strict mode
- Proper type annotations
- Kebab-case for component file names (`BeachCard.vue`)
- camelCase for methods and properties
- No `any` types without good reason

Example:
```typescript
interface Beach {
  id: number;
  name: string;
  owner_id: number;
}

export default defineComponent({
  name: 'BeachCard',
  props: {
    beach: {
      type: Object as PropType<Beach>,
      required: true,
    },
  },
  setup(props) {
    return {
      beach: computed(() => props.beach),
    };
  },
});
```

## Submitting changes

### 1. Push your branch
```bash
git push origin feature/your-feature
```

### 2. Open a Pull Request
- Clear title: describes what you did
- Description: explains why and how
- Reference issues if applicable
- Screenshot/video if UI changes

### 3. PR template
```markdown
## What
Describe what you changed.

## Why
Explain why this change is needed.

## How
How does it work? (optional if obvious)

## Testing
How did you test this?

## Checklist
- [ ] Tests pass
- [ ] No breaking changes
- [ ] Documentation updated
- [ ] Code follows style guide
```

### 4. Review process
- We'll review as soon as possible
- Comments aren't criticism - let's collaborate
- Make requested changes
- We'll merge when ready

## What we're looking for

### Security
- No SQL injection
- No XSS
- Input validation
- Ownership checks (if relevant)
- No credentials in code

### Performance
- No N+1 queries
- Efficient algorithms
- Reasonable request size

### Tests
- Feature tests for new endpoints
- Edge cases covered
- All tests pass

### Code quality
- Clear variable names
- No duplicate code
- Functions do one thing
- Comments where non-obvious

### Documentation
- Code commented if complex
- README updated if needed
- Commit messages clear

## Setting ADMIN_EMAILS

If you're testing admin features:

```bash
# In .env
ADMIN_EMAILS=yourname@example.com,another@example.com
```

Then restart:
```bash
make down && make up
```

## Need help?

- Check existing issues and PRs
- Ask in your PR
- Open a discussion issue
- Email maintainers

## Recognition

All contributors are listed in the project. Keep an eye on the [CONTRIBUTORS.md](./CONTRIBUTORS.md) file (we'll create one once we have contributions).

## Code of Conduct

Be respectful. We're all learning. Harassment, discrimination, or abuse won't be tolerated.

---

Thank you for contributing! 🙌
