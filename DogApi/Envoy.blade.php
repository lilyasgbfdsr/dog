@servers(['web' => 'root@47.94.201.240'])

@story('deploy')
    api
    vue
@endstory
    
@task('api')
    cd /var/www/DogApi
    git fetch
    git merge origin master
@endtask

@task('vue')
    cd /var/www/DogVue
    git fetch
    git merge origin master
    yarn build:prod
@endtask

@task('composer')
    cd /var/www/DogApi
    composer install
@endtask
