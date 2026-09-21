use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ldap_id')->nullable()->unique()->after('email');
            $table->boolean('is_ldap_user')->default(false)->after('ldap_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ldap_id', 'is_ldap_user']);
        });
    }
};