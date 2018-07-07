<?php namespace EvolutionCMS\Controllers\Resources;

use EvolutionCMS\Models;
use EvolutionCMS\Controllers\AbstractResources;
use EvolutionCMS\Interfaces\ManagerTheme\TabControllerInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent;

//'actions'=>array('edit'=>array(102,'edit_plugin'), 'duplicate'=>array(105,'new_plugin'), 'remove'=>array(104,'delete_plugin')),
class Plugins extends AbstractResources implements TabControllerInterface
{
    protected $view = 'page.resources.plugins';

    /**
     * @inheritdoc
     */
    public function getTabName(): string
    {
        return 'tabPlugins';
    }

    /**
     * @inheritdoc
     */
    public function canView(): bool
    {
        return evolutionCMS()->hasAnyPermissions([
            'new_plugin',
            'edit_plugin'
        ]);
    }

    protected function getBaseParams()
    {
        return array_merge(
            parent::getParameters(),
            [
                'tabPageName' => $this->getTabName(),
                'tabName' => 'site_plugins'
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getParameters(array $params = []) : array
    {
        $params = array_merge($this->getBaseParams(), $params);

        return $this->isNoData() ? $params : array_merge([
            'categories' => $this->parameterCategories(),
            'outCategory' => $this->parameterOutCategory(),
            'checkOldPlugins' => $this->checkOldPlugins()
        ], $params);
    }

    protected function parameterOutCategory(): Collection
    {
        return Models\SitePlugin::where('category', '=', 0)
            ->orderBy('name', 'ASC')
            ->lockedView()
            ->get();
    }

    protected function parameterCategories(): Collection
    {
        return Models\Category::with('plugins')
            ->whereHas('plugins', function (Eloquent\Builder $builder) {
                return $builder->lockedView();
            })->orderBy('rank', 'ASC')
            ->get();
    }

    // :TODO check old plugins
    protected function checkOldPlugins(): bool
    {
        return true;
    }
}