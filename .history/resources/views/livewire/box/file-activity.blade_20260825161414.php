<div>
    <div class="card border-left-danger shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        {{ __("Files")}} 
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <a data-toggle="modal" data-target="#file" class="btn btn-danger btn-sm" >Attach</a>
                        
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-question-circle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The critical single root wrapper -->
<div> 
    <div wire:ignore.self class="modal fade" id="file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
        <div class="modal-dialog" role="document"> <!-- Changed role="file" to "document" for Bootstrap compliance -->
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h5 class="modal-title" id="exampleModalLabel">Add File to Session</h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                        <span aria-hidden="true close-btn">×</span> 
                    </button> 
                </div> 
                <div class="modal-body"> 
                    @forelse ($files as $file) 
                        <!-- Added wire:key so Livewire tracks individual rows during pagination -->
                        <div wire:key="file-item-{{ $file->id }}">
                            <x-box.item :title="$file->title" :color="$file->color"> 
                                @slot('add') 
                                    {{ route('addFileToSession' , [ 'session' => $session, 'active_id' => $file->id ]) }} 
                                @endslot 
                            </x-box.item> <!-- Fixed mismatched closing tag -->
                        </div>
                    @empty
                        <p class="text-center text-muted">No files found.</p>
                    @endforelse 

                    <!-- Pagination Links Container -->
                    <div class="mt-3">
                        {{ $files->links() }} 
                    </div>
                </div> 
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button> 
                </div> 
            </div> 
        </div> 
    </div>
</div> <!-- End of root wrapper -->


<div wire:ignore.self class="modal fade" id="file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="file">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add File to Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
               
                @forelse ($files as $file)
                
                
                <x-box.item  
                :title="$file->title"
                :color="$file->color">
                @slot('add')
                {{ route('addFileToSession' , [
                    'session' => $session,
                    'active_id' => $file->id
                ]) }}   
                @endslot

               
                </x-container.File>
                
                @empty
                    
                @endforelse
            

            {{ $files->links() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div> 