<?php

namespace Pantheon\Terminus\Commands\Org\Site;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class ListCommand
 * @package Pantheon\Terminus\Commands\Org\Site
 */
class ListCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;
    use StructuredListTrait;

    /**
     * Displays the list of sites associated with an organization.
     *
     * @authorize
     * @filter-output
     *
     * @command org:site:list
     * @aliases org:sites
     *
     * @field-labels
     *     name: Name
     *     label: Label
     *     id: ID
     *     plan_name: Plan
     *     framework: Framework
     *     owner: Owner
     *     created: Created
     *     tags: Tags
     *     frozen: Is Frozen?
     * @default-fields name,id,plan_name,framework,owner,created,tags,frozen
     * @return RowsOfFields
     *
     * @param string $organization Organization name, label, or ID
     * @option plan DEPRECATED Plan filter; filter by the plan's label
     * @option string $tag DEPRECATED Tag name to filter
     * @option string $upstream Upstream name to filter
     *
     * @usage <organization> Displays the list of sites associated with <organization>.
     * @usage <organization> --plan=<plan> Displays the list of sites associated with <organization> having the plan named <plan>.
     * @usage <organization> --tag=<tag> Displays the list of sites associated with <organization> that have the <tag> tag.
     * @usage <organization> --upstream=<upstream> Displays the list of sites associated with <organization> with the upstream having UUID <upstream>.
     */
    public function listSites($organization, $options = ['plan' => null, 'tag' => null, 'upstream' => null,])
    {
        $org = $this->session()->getUser()->getOrganizationMemberships()->get($organization)->getOrganization();
        $this->sites->fetch(['org_id' => $org->id,]);
        if (isset($options['plan']) && !is_null($plan = $options['plan'])) {
            $this->sites->filterByPlanName($plan);
        }
        if (!is_null($tag = $options['tag'])) {
            $this->sites->filterByTag($tag);
        }
        if (!is_null($upstream = $options['upstream'])) {
            $this->sites->filterByUpstream($upstream);
        }
        return $this->getRowsOfFields(
            $this->sites,
            ['message' => 'This organization has no sites.',]
        );
    }
}
