import unittest
import datetime
import sys

from . import git_importer

class TestProcessCommitMessage(unittest.TestCase):
    def test_empty_message(self):
        expected_result = {}
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '  \n',
            False,
        ), expected_result)

    def test_merge_message(self):
        expected_result = {}
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "Merge branch 'development'\n",
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "Merge branch 'issue-23' into development\n",
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "Merge the branch 'issue-23' into the branch 'development'\n",
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "  Merge branch 'development'\n",
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "  Merge branch 'issue-23' into development\n",
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            "  Merge the branch 'issue-23' into the branch 'development'\n",
            False,
        ), expected_result)

    def test_message_without_issue_mark(self):
        expected_result = {
            git_importer.SPECIAL_ISSUE: ['update the change log'],
        }
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            'Update the change log\n',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '  Update the change log\n',
            False,
        ), expected_result)

    def test_multiline_message(self):
        expected_result = {git_importer.SPECIAL_ISSUE: [
            'revert "Issue #12: add the FizzBuzz class"',
        ]}
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '''Revert "Issue #12: add the FizzBuzz class"

This reverts commit 43065958923a14a05936887ccbb876d9dd5438f9.
''',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '''

Revert "Issue #12: add the FizzBuzz class"

This reverts commit 43065958923a14a05936887ccbb876d9dd5438f9.
''',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '''

{0}Revert "Issue #12: add the FizzBuzz class"{0}

This reverts commit 43065958923a14a05936887ccbb876d9dd5438f9.
'''.format('  '),
            False,
        ), expected_result)

    def test_message_with_one_issue_mark(self):
        expected_result = {'issue #12': ['add the FizzBuzz class']}
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            'Issue #12: add the FizzBuzz class\n',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '  Issue #12: add the FizzBuzz class\n',
            False,
        ), expected_result)

    def test_message_with_some_issues_marks(self):
        expected_result = {
            'issue #5': ['add the FizzBuzz class'],
            'issue #12': ['add the FizzBuzz class'],
        }
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            'Issue #5, issue #12: add the FizzBuzz class\n',
            False,
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '4306595',
            '  Issue #5, issue #12: add the FizzBuzz class\n',
            False,
        ), expected_result)

class TestProcessGitHistory(unittest.TestCase):
    def test_empty_commit_list(self):
        self.assertEqual(git_importer.process_git_history([], False), {})

    def test_unique_commits(self):
        timestamp_1 = datetime.datetime(2017, 5, 5)
        timestamp_2 = datetime.datetime(2017, 5, 12)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                '4306595',
                timestamp_1,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                '7299cd3',
                timestamp_2,
                'Issue #12: add the LinkedList class',
            ),
        ], False), {
            timestamp_1.date(): {'issue #5': ['add the FizzBuzz class']},
            timestamp_2.date(): {'issue #12': ['add the LinkedList class']},
        })

    def test_commits_with_same_timestamps(self):
        timestamp_1 = datetime.datetime(2017, 5, 5)
        timestamp_2 = datetime.datetime(2017, 5, 12, 2, 4, 6)
        timestamp_3 = datetime.datetime(2017, 5, 12, 12, 34, 56)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                '4306595',
                timestamp_1,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                '7299cd3',
                timestamp_1,
                'Issue #12: add the LinkedList class',
            ),
            git_importer.Commit(
                'b05b839',
                timestamp_2,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                '54f532c',
                timestamp_3,
                'Issue #12: add the LinkedList class',
            ),
        ], False), {
            timestamp_1.date(): {
                'issue #5': ['add the FizzBuzz class'],
                'issue #12': ['add the LinkedList class'],
            },
            timestamp_2.date(): {
                'issue #5': ['add the FizzBuzz class'],
                'issue #12': ['add the LinkedList class'],
            },
        })

    def test_commits_with_same_issues_marks(self):
        timestamp = datetime.datetime(2017, 5, 5)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                '4306595',
                timestamp,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                '7299cd3',
                timestamp,
                'Issue #5: add the LinkedList class',
            ),
        ], False), {timestamp.date(): {'issue #5': [
            'add the FizzBuzz class',
            'add the LinkedList class',
        ]}})

    def test_commits_with_some_issues_marks(self):
        timestamp = datetime.datetime(2017, 5, 5)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                '4306595',
                timestamp,
                'Issue #5, issue #12: add the FizzBuzz class',
            ),
            git_importer.Commit(
                '7299cd3',
                timestamp,
                'Issue #5, issue #12: add the LinkedList class',
            ),
        ], False), {timestamp.date(): {
            'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ],
            'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ],
        }})

class TestUniqueGitHistory(unittest.TestCase):
    def test_without_duplicates(self):
        data = {
            datetime.datetime(2017, 5, 5): {'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
            datetime.datetime(2017, 5, 12): {'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
        }
        self.assertEqual(git_importer.unique_git_history(data, False), data)

    def test_with_duplicates(self):
        timestamp_1 = datetime.datetime(2017, 5, 5)
        timestamp_2 = datetime.datetime(2017, 5, 12)
        self.assertEqual(git_importer.unique_git_history({
            timestamp_1: {'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
                'add the FizzBuzz class',
            ]},
            timestamp_2: {'issue #12': [
                'add the LinkedList class',
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
        }, False), {
            timestamp_1: {'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
            timestamp_2: {'issue #12': [
                'add the LinkedList class',
                'add the FizzBuzz class',
            ]},
        })

class TestFormatMessages(unittest.TestCase):
    def test_one_messages(self):
        self.assertEqual(git_importer.format_messages(
            'Test Project, ',
            'issue #12',
            ['add the FizzBuzz class'],
            False,
        ), 'Test Project, issue #12, add the FizzBuzz class')

    def test_some_messages(self):
        self.assertEqual(git_importer.format_messages(
            'Test Project, ',
            'issue #12',
            [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ],
            False,
        ), '''Test Project, issue #12, add the FizzBuzz class
        add the LinkedList class''')

class TestGetIssueMarkKey(unittest.TestCase):
    def test_common_issue(self):
        self.assertEqual(git_importer.get_issue_mark_key(('issue #12',)), 12)

    def test_special_issue(self):
        self.assertEqual(git_importer.get_issue_mark_key(
            (git_importer.SPECIAL_ISSUE,),
        ), sys.maxsize)

class TestFormatIssuesMarks(unittest.TestCase):
    def test_one_issue_mark(self):
        self.assertEqual(git_importer.format_issues_marks(
            'Test Project',
            datetime.datetime(2017, 5, 5),
            {'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
            False,
        ), '''## 2017-05-05

```
Test Project, issue #12, add the FizzBuzz class
        add the LinkedList class
```''')

    def test_some_issues_marks(self):
        self.assertEqual(git_importer.format_issues_marks(
            'Test Project',
            datetime.datetime(2017, 5, 5),
            {
                'issue #5': [
                    'add the FizzBuzz class',
                    'add the LinkedList class',
                ],
                'issue #12': [
                    'add the FizzBuzz class',
                    'add the LinkedList class',
                ],
            },
            False,
        ), '''## 2017-05-05

```
Test Project, issue #5, add the FizzBuzz class
        add the LinkedList class

    issue #12, add the FizzBuzz class
        add the LinkedList class
```''')

class TestFormatGitHistory(unittest.TestCase):
    def test_one_timestamp(self):
        self.assertEqual(git_importer.format_git_history('Test Project', {
            datetime.datetime(2017, 5, 5): {'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
        }, False), '''# Test Project

## 2017-05-05

```
Test Project, issue #12, add the FizzBuzz class
        add the LinkedList class
```''')

    def test_some_timestamps(self):
        self.assertEqual(git_importer.format_git_history('Test Project', {
            datetime.datetime(2017, 5, 5): {'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
            datetime.datetime(2017, 5, 12): {'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ]},
        }, False), '''# Test Project

## 2017-05-05

```
Test Project, issue #5, add the FizzBuzz class
        add the LinkedList class
```

## 2017-05-12

```
Test Project, issue #12, add the FizzBuzz class
        add the LinkedList class
```''')
