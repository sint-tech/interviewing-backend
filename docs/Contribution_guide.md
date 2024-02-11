### You need to follow the next steps for contribution to the sint-backend repository
-----
1. Ensure the **development branch** is updated on your local machine.
2. Write your changes on **new branch**, and ensure the branch name follows the branch name conventions.
3. Open **new PR** to the **development**
4. Ensure you have documented the PR with your changes.
---
### To submit your code review on  PR
1. Ensure you have read the related ticket for this PR.
2. PR documentation is valid and there is no misunderstanding description on this PR.
3. all test cases had passed.
4. if there are change requests, fill it with review comments and request new changes.

---
### Merging changes to development
   After approving the PR, you must rebase your code with the **Development branch**, So you may do it on your local machine or the GitHub website.
 - For using the GitHub application check this [URL](https://docs.github.com/en/desktop/managing-commits/squashing-commits-in-github-desktop)
 - Using CLI
  ```git
git checkout development
git pull origin development
git checkout #branch name
git rebase development
git push --force
```

- Using Github, you will see a button called **Update your branch** just ensure to select rebase instead of merge
> [!WARNING]
> u may use the regular ``merge`` command when there are conflicts between the two branches (development, and your branch)

Now, once the `current branch` is ready for merging into the development. U should [squash](https://www.git-tower.com/learn/git/faq/git-squash) the commits into development branch 🚀
> [!IMPORTANT]
> If your changes are hotfixes, and need to be merged also on **production**.
> You have also to follow these steps
> ```git
> git checkout master
> git checkout -b #new-branch-from-master
> git cherry-pick #commit-hash #commit merged to development
> ```
