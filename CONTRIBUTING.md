# Contribution Guide
Welcome to **Todolist**, a powerful tool designed to simplify your daily life. We understand how challenging it can be to keep track of all the tasks you need to accomplish every day, whether at home, at work, or anywhere else. That's why we've created this user-friendly application that allows you to quickly capture, organize, and track your tasks with ease.

## To begin
If you wish to contribute to the success of this wonderful project, start by forking the repository. Next, clone the project and install its dependencies. You can do this by following the instructions provided in the [README.md](README.md) file at the root of the project.

## Commit message formatting
In order to maintain a certain consistency, a commit message should adhere to the following rules :
```
<type>(<optional scope>): <subject>
```

### Types
* Relevant changes
    * `feat` Commits, that adds a new feature
    * `fix` Commits, that fixes a bug
* `refactor` Commits, that rewrite/restructure your code, however does not change any behaviour
    * `perf` Commits are special `refactor` commits, that improve performance
* `style` Commits, that do not affect the meaning (white-space, formatting, missing semi-colons, etc)
* `test` Commits, that add missing tests or correcting existing tests
* `docs` Commits, that affect documentation only
* `build` Commits, that affect build components like build tool, ci pipeline, dependencies, project version, ...
* `ops` Commits, that affect operational components like infrastructure, deployment, backup, recovery, ...
* `chore` Miscellaneous commits e.g. modifying `.gitignore`

### Scopes
The `scope` provides additional contextual information.
* Is an **optional** part of the format
* Allowed Scopes depends on the specific project
* Don't use issue identifiers as scopes

### Subject
The `subject` contains a succinct description of the change.
* Is a **mandatory** part of the format
* Use the imperative, present tense: "change" not "changed" nor "changes"
* No dot (.) at the end

## Code Quality
In order to maintain a performant and maintainable codebase, we encourage you to follow the following rules:
* Adhere to PHP programming standards (PSR).
* Use camelCase for variables and methods.
* Use descriptive and English names for variables, functions, and classes.
* Prefix boolean variables with "is."
* Comment the code when necessary and include PHP Doc.

## Tests
If you're adding a new feature or modifying an existing function, make sure to create appropriate unit tests to cover your code.
Run unit and functional tests using PHPUnit to ensure that your changes do not introduce errors.

## Pull Request
After following the previous steps, you can submit a pull request (PR). The PR will then be reviewed, validated, and merged if it meets the project's various requirements.
