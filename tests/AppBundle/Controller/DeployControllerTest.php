<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AuthClientTrait;

class DeployControllerTest extends WebTestCase
{
    use AuthClientTrait;

    public function testPayload()
    {
        $client = $this->getClient();

        $crawler = $client->request('POST', '/payload',
            array(),
            array(),
            array(
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_User-Agent'        => 'GitHub-Hookshot/886c556',
                'HTTP_X-GitHub-Delivery' => 'ec46b900-fe77-11e6-9c76-f4231ba8608e',
                'HTTP_X-GitHub-Event'    => 'push',
                'HTTP_X-Hub-Signature'   => 'sha1=517b587cdcd893270d446ab6e8238d136a1e1802',
            ),
            $this->getGitPayload()
        );

        dd($crawler->html());


//        $client = static::createClient();
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    private function getGitPayload()
    {

        return '{"ref":"refs/heads/pre","before":"969afdce0ed4a1455d550addb4e072488beeb364","after":"d05694cdf67c4647ef609de86656d01a19da1fbb","created":false,"deleted":false,"forced":false,"base_ref":null,"compare":"https://github.com/NadirZenith/nzlab.es/compare/969afdce0ed4...d05694cdf67c","commits":[{"id":"d05694cdf67c4647ef609de86656d01a19da1fbb","tree_id":"b933c12069088fc6cc0637383cedd066026babab","distinct":true,"message":"fix typos","timestamp":"2017-03-01T13:09:24+01:00","url":"https://github.com/NadirZenith/nzlab.es/commit/d05694cdf67c4647ef609de86656d01a19da1fbb","author":{"name":"NadirZenith","email":"2cb.md2@gmail.com","username":"NadirZenith"},"committer":{"name":"NadirZenith","email":"2cb.md2@gmail.com","username":"NadirZenith"},"added":[],"removed":[],"modified":["deploy/deploy.sh"]}],"head_commit":{"id":"d05694cdf67c4647ef609de86656d01a19da1fbb","tree_id":"b933c12069088fc6cc0637383cedd066026babab","distinct":true,"message":"fix typos","timestamp":"2017-03-01T13:09:24+01:00","url":"https://github.com/NadirZenith/nzlab.es/commit/d05694cdf67c4647ef609de86656d01a19da1fbb","author":{"name":"NadirZenith","email":"2cb.md2@gmail.com","username":"NadirZenith"},"committer":{"name":"NadirZenith","email":"2cb.md2@gmail.com","username":"NadirZenith"},"added":[],"removed":[],"modified":["deploy/deploy.sh"]},"repository":{"id":54742947,"name":"nzlab.es","full_name":"NadirZenith/nzlab.es","owner":{"name":"NadirZenith","email":"2cb.md2@gmail.com","login":"NadirZenith","id":5464337,"avatar_url":"https://avatars3.githubusercontent.com/u/5464337?v=3","gravatar_id":"","url":"https://api.github.com/users/NadirZenith","html_url":"https://github.com/NadirZenith","followers_url":"https://api.github.com/users/NadirZenith/followers","following_url":"https://api.github.com/users/NadirZenith/following{/other_user}","gists_url":"https://api.github.com/users/NadirZenith/gists{/gist_id}","starred_url":"https://api.github.com/users/NadirZenith/starred{/owner}{/repo}","subscriptions_url":"https://api.github.com/users/NadirZenith/subscriptions","organizations_url":"https://api.github.com/users/NadirZenith/orgs","repos_url":"https://api.github.com/users/NadirZenith/repos","events_url":"https://api.github.com/users/NadirZenith/events{/privacy}","received_events_url":"https://api.github.com/users/NadirZenith/received_events","type":"User","site_admin":false},"private":false,"html_url":"https://github.com/NadirZenith/nzlab.es","description":null,"fork":false,"url":"https://github.com/NadirZenith/nzlab.es","forks_url":"https://api.github.com/repos/NadirZenith/nzlab.es/forks","keys_url":"https://api.github.com/repos/NadirZenith/nzlab.es/keys{/key_id}","collaborators_url":"https://api.github.com/repos/NadirZenith/nzlab.es/collaborators{/collaborator}","teams_url":"https://api.github.com/repos/NadirZenith/nzlab.es/teams","hooks_url":"https://api.github.com/repos/NadirZenith/nzlab.es/hooks","issue_events_url":"https://api.github.com/repos/NadirZenith/nzlab.es/issues/events{/number}","events_url":"https://api.github.com/repos/NadirZenith/nzlab.es/events","assignees_url":"https://api.github.com/repos/NadirZenith/nzlab.es/assignees{/user}","branches_url":"https://api.github.com/repos/NadirZenith/nzlab.es/branches{/branch}","tags_url":"https://api.github.com/repos/NadirZenith/nzlab.es/tags","blobs_url":"https://api.github.com/repos/NadirZenith/nzlab.es/git/blobs{/sha}","git_tags_url":"https://api.github.com/repos/NadirZenith/nzlab.es/git/tags{/sha}","git_refs_url":"https://api.github.com/repos/NadirZenith/nzlab.es/git/refs{/sha}","trees_url":"https://api.github.com/repos/NadirZenith/nzlab.es/git/trees{/sha}","statuses_url":"https://api.github.com/repos/NadirZenith/nzlab.es/statuses/{sha}","languages_url":"https://api.github.com/repos/NadirZenith/nzlab.es/languages","stargazers_url":"https://api.github.com/repos/NadirZenith/nzlab.es/stargazers","contributors_url":"https://api.github.com/repos/NadirZenith/nzlab.es/contributors","subscribers_url":"https://api.github.com/repos/NadirZenith/nzlab.es/subscribers","subscription_url":"https://api.github.com/repos/NadirZenith/nzlab.es/subscription","commits_url":"https://api.github.com/repos/NadirZenith/nzlab.es/commits{/sha}","git_commits_url":"https://api.github.com/repos/NadirZenith/nzlab.es/git/commits{/sha}","comments_url":"https://api.github.com/repos/NadirZenith/nzlab.es/comments{/number}","issue_comment_url":"https://api.github.com/repos/NadirZenith/nzlab.es/issues/comments{/number}","contents_url":"https://api.github.com/repos/NadirZenith/nzlab.es/contents/{+path}","compare_url":"https://api.github.com/repos/NadirZenith/nzlab.es/compare/{base}...{head}","merges_url":"https://api.github.com/repos/NadirZenith/nzlab.es/merges","archive_url":"https://api.github.com/repos/NadirZenith/nzlab.es/{archive_format}{/ref}","downloads_url":"https://api.github.com/repos/NadirZenith/nzlab.es/downloads","issues_url":"https://api.github.com/repos/NadirZenith/nzlab.es/issues{/number}","pulls_url":"https://api.github.com/repos/NadirZenith/nzlab.es/pulls{/number}","milestones_url":"https://api.github.com/repos/NadirZenith/nzlab.es/milestones{/number}","notifications_url":"https://api.github.com/repos/NadirZenith/nzlab.es/notifications{?since,all,participating}","labels_url":"https://api.github.com/repos/NadirZenith/nzlab.es/labels{/name}","releases_url":"https://api.github.com/repos/NadirZenith/nzlab.es/releases{/id}","deployments_url":"https://api.github.com/repos/NadirZenith/nzlab.es/deployments","created_at":1458934172,"updated_at":"2016-03-25T22:55:26Z","pushed_at":1488370170,"git_url":"git://github.com/NadirZenith/nzlab.es.git","ssh_url":"git@github.com:NadirZenith/nzlab.es.git","clone_url":"https://github.com/NadirZenith/nzlab.es.git","svn_url":"https://github.com/NadirZenith/nzlab.es","homepage":null,"size":1757,"stargazers_count":0,"watchers_count":0,"language":"PHP","has_issues":true,"has_downloads":true,"has_wiki":true,"has_pages":false,"forks_count":0,"mirror_url":null,"open_issues_count":0,"forks":0,"open_issues":0,"watchers":0,"default_branch":"master","stargazers":0,"master_branch":"master"},"pusher":{"name":"NadirZenith","email":"2cb.md2@gmail.com"},"sender":{"login":"NadirZenith","id":5464337,"avatar_url":"https://avatars3.githubusercontent.com/u/5464337?v=3","gravatar_id":"","url":"https://api.github.com/users/NadirZenith","html_url":"https://github.com/NadirZenith","followers_url":"https://api.github.com/users/NadirZenith/followers","following_url":"https://api.github.com/users/NadirZenith/following{/other_user}","gists_url":"https://api.github.com/users/NadirZenith/gists{/gist_id}","starred_url":"https://api.github.com/users/NadirZenith/starred{/owner}{/repo}","subscriptions_url":"https://api.github.com/users/NadirZenith/subscriptions","organizations_url":"https://api.github.com/users/NadirZenith/orgs","repos_url":"https://api.github.com/users/NadirZenith/repos","events_url":"https://api.github.com/users/NadirZenith/events{/privacy}","received_events_url":"https://api.github.com/users/NadirZenith/received_events","type":"User","site_admin":false}}';

    }
}
